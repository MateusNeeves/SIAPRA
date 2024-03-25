<?php

namespace App\Http\Controllers;

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
        $planejamento = Planejamento::where('data_producao', $request->data_producao)->first();

        if ($planejamento == null)
            return back()->with('alert-dark', 'Não há nenhum planejamento para essa data.')->withInput(); 

        $dur_ciclotron_hora = (int) $planejamento->duracao_ciclotron;
        $dur_ciclotron_min = ceil(($planejamento->duracao_ciclotron - $dur_ciclotron_hora)*60);
        
        $dur_ciclotron_print = (($dur_ciclotron_hora < 10) ? '0'.$dur_ciclotron_hora : $dur_ciclotron_hora) . ':' . (($dur_ciclotron_min < 10) ? '0'.$dur_ciclotron_min : $dur_ciclotron_min);
        
        $pedidos_plan = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, P.QTD_DOSES, C.TEMPO_TRANSP, PP.ATIV_DEST, PP.VOL_FRASCO
        FROM PEDIDOS P INNER JOIN PEDIDOS_PLAN PP ON (P.ID = PP.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
        WHERE PP.ID_PLANEJAMENTO = ?', [$planejamento->id]));
        
        return back()->with(['planejamento' => $planejamento, 'pedidos_plan' => $pedidos_plan, 'dur_ciclotron' => $dur_ciclotron_print])->withInput();
    }

    public function register(){
        $parametros = Parametro::first();
        return view('planejamentos/cadastrar', ['parametros'=> $parametros]);
    }

    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['data_producao' => 'unique:planejamentos'],
            
            ['data_producao.unique' => 'Já existe um planejamento nessa data']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     
    
        $tempo_meia_vida = 109.7;
        
        $pedidos = Pedido::where('data_entrega', $request['data_producao'])->get();

        if(!count($pedidos))
            return redirect()->back()->with('alert-warning', 'Nenhum registro encontrado.')->withInput();
        
        $a = 0;
        $eos_frascos = 0;

        foreach ($pedidos as $pedido){
            // CLIENTE: NOME_FANTASIA E TEMPO_TRANSP
            $infos_cliente = Cliente::select('nome_fantasia', 'tempo_transp')->where('id', $pedido['id_cliente'])->first();
            
            //VARIAVEIS
            $cod_ped = $pedido['id'];
            $nome_cliente = $infos_cliente['nome_fantasia'];
            $qtd_doses = $pedido['qtd_doses'];
            $tempo_transp = $infos_cliente['tempo_transp'];

            //ATIVIDADE NO DESTINO
            $ativ_dest = 0;
            for ($i = 0 ; $i < $qtd_doses ; $i++)
                $ativ_dest = $request['ativ_dose'] + ($ativ_dest * exp(M_LN2 * $request['tempo_exames'] / $tempo_meia_vida));

            //ATIVIDADE AO SAIR
            $ativ_crcn = $ativ_dest * exp(M_LN2 * $tempo_transp / $tempo_meia_vida);
            
            //ATIVIDADE EOS POR FRASCO
            $ativ_eos_frasco = $ativ_crcn * exp(M_LN2 * $request['tempo_exped'] / $tempo_meia_vida);
            
            //ATIVIDADE TOTAL DOS FRASCOS
            $eos_frascos += $ativ_eos_frasco;

            $ativ_dest = sprintf("%.1f",$ativ_dest);
            $ativ_crcn = sprintf("%.1f",$ativ_crcn);
            $ativ_eos_frasco = sprintf("%.1f",$ativ_eos_frasco);

            $infos[$a] = [$cod_ped, $nome_cliente, $qtd_doses, $tempo_transp, $ativ_dest, $ativ_crcn, $ativ_eos_frasco];
            $a++;
        }

        // LOOP CONTROLE DE QUALIDADE
        $ativ_cq = 0;
        $vol_cq = 0;

        while($request['vol_max_cq'] - $vol_cq >= 0.1){
            $ativ_cq += 0.1;
            $eob = ($eos_frascos + $ativ_cq) / ($request['rend_sintese'] / 100);
            $eos = $eob * ($request['rend_sintese'] / 100);
            $ativ_esp = $eos / $request['vol_eos'];
            $vol_cq = $ativ_cq / $ativ_esp;
        }

        // VOLUME EM CADA FRASCO
        for ($i = 0 ; $i < count($pedidos) ; $i++){
            $vol_frasco = $infos[$i][6] / $ativ_esp;
            $vol_frasco = sprintf("%.1f",$vol_frasco);
            $infos[$i][7] = $vol_frasco;
        }

        //DURAÇÃO DO CÍCLOTRON
        $dur_ciclotron = -($tempo_meia_vida/M_LN2) * log(1 - ($eob/($request['corrente_alvo'] *$request['rend_tip_ciclotron']))) / 60;
        
        $dur_ciclotron_hora = (int) $dur_ciclotron;
        $dur_ciclotron_min = ceil(($dur_ciclotron - $dur_ciclotron_hora)*60);

        $dur_ciclotron_print = (($dur_ciclotron_hora < 10) ? '0'.$dur_ciclotron_hora : $dur_ciclotron_hora) . ':' . (($dur_ciclotron_min < 10) ? '0'.$dur_ciclotron_min : $dur_ciclotron_min);
        
        $eos = sprintf("%.1f",$eos);
        $eob = sprintf("%.1f",$eob);
        $ativ_esp = sprintf("%.1f",$ativ_esp);
        
        if ($request->action == 'calculate'){
            return redirect()->back()->with(['dur_ciclotron' => $dur_ciclotron_print, 'eob' => $eob, 'eos'=> $eos, 'ativ_esp' => $ativ_esp, 'infos' => $infos])->withInput();
        }
        else if ($request->action == 'save'){
            try{
                DB::beginTransaction();

                $planejamento = new Planejamento;
                $planejamento->id_usuario = Auth::user()->id;
                $planejamento->data_producao = $request->data_producao;
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
                    $pedidos_plan[$i]->id_pedido = $infos[$i][0];
                    $pedidos_plan[$i]->id_planejamento = $planejamento->id;
                    $pedidos_plan[$i]->ativ_dest = $infos[$i][4];
                    $pedidos_plan[$i]->vol_frasco = $infos[$i][7];
                    
                    $pedidos_plan[$i]->save();
                }

                DB::commit();
                return redirect('planejamentos')->with('alert-success', 'Planejamento realizado com sucesso.');
            }
            catch (\Exception $exception) {
                DB::rollback();
                return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput(); 
            }
        }   
    }
}
