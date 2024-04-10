<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Parametro;
use App\Models\Pedido_Plan;
use App\Models\Planejamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\UniqueConstraintViolationException;

class PlanejamentosController extends Controller
{
    public function index(){
        return view('planejamentos/visualizar');
    }

    public function show(Request $request){
        $planejamentos = Planejamento::where('data_producao', $request->data_producao)->get();

        if (!count($planejamentos))
            return redirect()->back()->with('alert-dark', 'Não há nenhum planejamento para essa data.')->withInput(); 
        
        foreach ($planejamentos as $idx => $planejamento) {
            $dur_ciclotron_hora = (int) $planejamento->duracao_ciclotron;
            $dur_ciclotron_min = ceil(($planejamento->duracao_ciclotron - $dur_ciclotron_hora)*60);
            
            $dur_ciclotron_print[$idx] = (($dur_ciclotron_hora < 10) ? '0'.$dur_ciclotron_hora : $dur_ciclotron_hora) . ':' . (($dur_ciclotron_min < 10) ? '0'.$dur_ciclotron_min : $dur_ciclotron_min);
            
            $pedidos_plan[$idx] = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, PP.QTD_DOSES_SELEC, C.TEMPO_TRANSP, PP.ATIV_DEST, PP.VOL_FRASCO
            FROM PEDIDOS P INNER JOIN PEDIDOS_PLAN PP ON (P.ID = PP.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
            WHERE PP.ID_PLANEJAMENTO = ?', [$planejamento->id]));

            $hora_saida = Carbon::createFromFormat("h:i:s", $planejamento->hora_saida);
            $fim_sintese = $hora_saida->subMinutes($planejamento->tempo_exped);
                $fim_sintese_str[$idx] = $fim_sintese->format('h:i');
            $inicio_sintese = $fim_sintese->subMinutes($planejamento->tempo_sintese);
                $inicio_sintese_str[$idx] = $inicio_sintese->format('h:i');
            $fim_ciclotron = $inicio_sintese->subMinutes(5);
                $fim_ciclotron_str[$idx] = $fim_ciclotron->format('h:i');
            $inicio_ciclotron = $fim_ciclotron->subHour($planejamento->duracao_ciclotron);
                $inicio_ciclotron_str[$idx] = $inicio_ciclotron->format('h:i');
            

        }
        return redirect()->back()->with(['planejamentos' => $planejamentos, 'pedidos_plan' => $pedidos_plan, 'dur_ciclotron' => $dur_ciclotron_print, 'inicio_sintese' => $inicio_sintese_str, 'fim_sintese' => $fim_sintese_str, 'inicio_ciclotron' => $inicio_ciclotron_str, 'fim_ciclotron' => $fim_ciclotron_str])->withInput();
    }

    public function register(){
        $parametros = Parametro::first();
        return view('planejamentos/cadastrar', ['parametros'=> $parametros]);
    }

    public function store(Request $request){
        $tempo_meia_vida = 109.7;

        $pedidos = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, P.QTD_DOSES - (SELECT coalesce(SUM(PP.QTD_DOSES_SELEC), 0) FROM PEDIDOS_PLAN PP WHERE PP.ID_PEDIDO = P.ID) AS qtd_doses, C.TEMPO_TRANSP
                                        FROM PEDIDOS P INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID)
                                        WHERE DATA_ENTREGA = ?
                                        AND P.QTD_DOSES - (SELECT coalesce(SUM(PP.QTD_DOSES_SELEC), 0) FROM PEDIDOS_PLAN PP WHERE PP.ID_PEDIDO = P.ID) > 0', [$request->data_producao]));
        if(!count($pedidos))
            return redirect()->back()->with('alert-warning', 'Nenhum pedido encontrado para o dia ' . $request->data_producao)->withInput();
        
        if ($request->action == 'show'){
            return redirect()->back()->with(['pedidos' => $pedidos])->withInput();
        }

        // RETIRAR OS PEDIDOS QUE FORAM 'DESCELECIONADOS'
        $qtd_doses_total = 0;
        foreach ($pedidos as $idx => $pedido) 
            $qtd_doses_total += $request->qtd_doses_selec[$idx];
        if ($qtd_doses_total == 0)
         return redirect()->back()->with(['pedidos' => $pedidos])->with('alert-danger', 'Quantidade de Doses não pode ser igual a zero.')->withInput();
    
        $eos_frascos = 0;

        foreach ($pedidos as $idx => $pedido){
            //ATIVIDADE NO DESTINO
            $ativ_dest = 0;
            for ($i = 0 ; $i < $request->qtd_doses_selec[$idx] ; $i++)
                $ativ_dest = $request->ativ_dose + ($ativ_dest * exp(M_LN2 * $request->tempo_exames / $tempo_meia_vida));

            //ATIVIDADE AO SAIR
            $ativ_crcn = $ativ_dest * exp(M_LN2 * $pedido->tempo_transp / $tempo_meia_vida);
            
            //ATIVIDADE EOS POR FRASCO
            $ativ_eos_frasco = $ativ_crcn * exp(M_LN2 * $request->tempo_exped / $tempo_meia_vida);
            
            //ATIVIDADE TOTAL DOS FRASCOS
            $eos_frascos += $ativ_eos_frasco;

            $ativ_dest = sprintf("%.1f",$ativ_dest);
            $ativ_crcn = sprintf("%.1f",$ativ_crcn);
            $ativ_eos_frasco = sprintf("%.1f",$ativ_eos_frasco);

            $pedidos[$idx]->ativ_dest = $ativ_dest;
            $pedidos[$idx]->ativ_crcn = $ativ_crcn;
            $pedidos[$idx]->ativ_eos_frasco = $ativ_eos_frasco;
        }

        // LOOP CONTROLE DE QUALIDADE
        $ativ_cq = 0;
        $vol_cq = 0;

        while($request->vol_max_cq - $vol_cq >= 0.1){
            $ativ_cq += 0.1;
            $eob = ($eos_frascos + $ativ_cq) / ($request->rend_sintese / 100);
            $eos = $eob * ($request->rend_sintese / 100);
            $ativ_esp = $eos / $request->vol_eos;
            $vol_cq = $ativ_cq / $ativ_esp;
        }

        // VOLUME EM CADA FRASCO
        for ($i = 0 ; $i < count($pedidos) ; $i++){
            $vol_frasco = $pedidos[$i]->ativ_eos_frasco / $ativ_esp;
            $vol_frasco = sprintf("%.1f",$vol_frasco);
            $pedidos[$i]->vol_frasco = $vol_frasco;
        }

        //APLICAÇÃO DO FATOR DE SEGURANÇA
        $eob += $eob * $request->fator_seguranca / 100;

        //DURAÇÃO DO CÍCLOTRON
        $dur_ciclotron = -($tempo_meia_vida/M_LN2) * log(1 - ($eob/($request->corrente_alvo *$request->rend_tip_ciclotron))) / 60;
        
        $dur_ciclotron_hora = (int) $dur_ciclotron;
        $dur_ciclotron_min = ceil(($dur_ciclotron - $dur_ciclotron_hora)*60);

        $dur_ciclotron_print = (($dur_ciclotron_hora < 10) ? '0'.$dur_ciclotron_hora : $dur_ciclotron_hora) . ':' . (($dur_ciclotron_min < 10) ? '0'.$dur_ciclotron_min : $dur_ciclotron_min);
        
        $eos = sprintf("%.1f",$eos);
        $eob = sprintf("%.1f",$eob);
        $ativ_esp = sprintf("%.1f",$ativ_esp);

        $hora_saida = Carbon::createFromFormat("h:i:s", $request->hora_saida);
        $fim_sintese = $hora_saida->subMinutes($request->tempo_exped);
            $fim_sintese_str = $fim_sintese->format('h:i');
        $inicio_sintese = $fim_sintese->subMinutes($request->tempo_sintese);
            $inicio_sintese_str = $inicio_sintese->format('h:i');
        $fim_ciclotron = $inicio_sintese->subMinutes(5);
            $fim_ciclotron_str = $fim_ciclotron->format('h:i');
        $inicio_ciclotron = $fim_ciclotron->subHour($dur_ciclotron);
            $inicio_ciclotron_str = $inicio_ciclotron->format('h:i');

        if ($request->action == 'calculate'){
            return redirect()->back()->with(['dur_ciclotron' => $dur_ciclotron_print, 'eob' => $eob, 'eos'=> $eos, 'ativ_esp' => $ativ_esp, 'pedidos' => $pedidos, 'inicio_sintese' => $inicio_sintese_str, 'fim_sintese' => $fim_sintese_str, 'inicio_ciclotron' => $inicio_ciclotron_str, 'fim_ciclotron' => $fim_ciclotron_str])->withInput();
        }
        else if ($request->action == 'save'){
            // RETIRAR OS PEDIDOS QUE FORAM 'DESCELECIONADOS'
            foreach ($pedidos as $idx => $pedido) 
                if ($request->qtd_doses_selec[$idx] == 0)
                    unset($pedidos[$idx]);

            try{
                DB::beginTransaction();

                $planejamento = new Planejamento;
                $planejamento->id_usuario = Auth::user()->id;
                $planejamento->data_producao = $request->data_producao;
                $planejamento->fator_seguranca = $request->fator_seguranca;
                $planejamento->ativ_dose = $request->ativ_dose;
                $planejamento->tempo_exames = $request->tempo_exames;
                $planejamento->vol_max_cq = $request->vol_max_cq;
                $planejamento->tempo_exped = $request->tempo_exped;
                $planejamento->rend_tip_ciclotron = $request->rend_tip_ciclotron;
                $planejamento->corrente_alvo = $request->corrente_alvo;
                $planejamento->rend_sintese = $request->rend_sintese;
                $planejamento->tempo_sintese = $request->tempo_sintese;
                $planejamento->vol_eos = $request->vol_eos;
                $planejamento->hora_saida = $request->hora_saida;
                $planejamento->duracao_ciclotron = $dur_ciclotron;
                $planejamento->ativ_eob = $eob;
                $planejamento->ativ_eos = $eos;
                $planejamento->ativ_esp = $ativ_esp;
                
                $planejamento->save();

                for ($i=0; $i < count($pedidos); $i++) { 
                    $pedidos_plan[$i] = new Pedido_Plan;
                    $pedidos_plan[$i]->id_pedido = $pedidos[$i]->id;
                    $pedidos_plan[$i]->id_planejamento = $planejamento->id;
                    $pedidos_plan[$i]->qtd_doses_selec = $request->qtd_doses_selec[$i];
                    $pedidos_plan[$i]->ativ_dest = $pedidos[$i]->ativ_dest;
                    $pedidos_plan[$i]->vol_frasco = $pedidos[$i]->vol_frasco;
                    
                    $pedidos_plan[$i]->save();
                }

                DB::commit();
                return redirect()->route('planejamentos')->with('alert-success', 'Planejamento realizado com sucesso.');
            }
            catch (\Exception $exception) {
                DB::rollback();
                return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput(); 
            }
        }   
    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();

            $pedidos_plan = Pedido_Plan::where('id_planejamento', $request->id)->get();

            foreach ($pedidos_plan as $pedido_plan)
                $pedido_plan->delete();

            Planejamento::find($request->id)->delete();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Planejamento excluído com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Planejamento, pois outras informações dependem dele.');
        } 
    }
}
