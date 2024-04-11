<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\Planejamento;
use App\Models\Pedido_Frac;
use Illuminate\Http\Request;
use App\Models\Fracionamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FracionamentosController extends Controller{
    public function index(){
        return view('fracionamentos/visualizar');
    }

    public function show(Request $request){
        $fracionamentos = Fracionamento::where('data_producao', $request->data_producao)->get();

        if (!count($fracionamentos))
            return back()->with('alert-dark', 'Não há nenhum Fracionamento para essa data.')->withInput(); 
        
        foreach ($fracionamentos as $idx => $fracionamento) {
            $pedidos_frac[$idx] = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, PF.QTD_DOSES_SELEC, C.TEMPO_TRANSP, PF.VOL_REAL_FRASCO
            FROM PEDIDOS P INNER JOIN PEDIDOS_FRAC PF ON (P.ID = PF.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
            WHERE PF.ID_FRACIONAMENTO = ?', [$fracionamento->id]));
        }

        return redirect()->back()->with(['fracionamentos' => $fracionamentos, 'pedidos_frac' => $pedidos_frac])->withInput();
    }

    public function register(){
        $fracionamentos = Fracionamento::select('id_planejamento')->where('data_producao', today())->get();
        $planejamento = Planejamento::where('data_producao', today())->whereNotIn('id', $fracionamentos)->first();

        if(!$planejamento)
            return redirect()->back()->with('alert-dark', 'Nenhuma Produção Planejada para Hoje.')->withInput();
        
        $pedidos_plan = collect(DB::select('SELECT PP.ID_PEDIDO, C.NOME_FANTASIA, PP.QTD_DOSES_SELEC, C.TEMPO_TRANSP, PP.ATIV_DEST 
                                            FROM PEDIDOS P INNER JOIN PEDIDOS_PLAN PP ON (P.ID = PP.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
                                            WHERE PP.ID_PLANEJAMENTO = ?
                                            ORDER BY PP.ID_PEDIDO', [$planejamento->id]));
        return view('fracionamentos/cadastrar', ['planejamento' => $planejamento, 'pedidos_plan' => $pedidos_plan, 'qtd' => count($fracionamentos) + 1]);
    }

    public function store(Request $request){
            $planejamento = Planejamento::where('data_producao', today())->whereNotIn('id', (Fracionamento::select('id_planejamento')->where('data_producao', today())->get()))->first();

            $pedidos_plan = collect(DB::select('SELECT PP.ID_PEDIDO, C.NOME_FANTASIA, PP.QTD_DOSES_SELEC, C.TEMPO_TRANSP, PP.ATIV_DEST
                                                FROM PEDIDOS P INNER JOIN PEDIDOS_PLAN PP ON (P.ID = PP.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
                                                WHERE PP.ID_PLANEJAMENTO = ?
                                                ORDER BY PP.ID_PEDIDO', [$planejamento->id]));

            $tempo_meia_vida = 109.7;
            $fim_sintese = Carbon::createFromFormat("H:i:s", date('H:i:s'));
            $hora_saida = Carbon::createFromFormat("H:i:s", date('H:i:s'))->addMinutes($planejamento->tempo_exped);
            $ativ_eos_real_mCi = $request->ativ_eos_real / 37;
            $rend_sintese_real = $ativ_eos_real_mCi * 100 / $request->ativ_eob_real;
            $rend_sintese_real = sprintf("%.1f", $rend_sintese_real);
            $ativ_esp = $ativ_eos_real_mCi / $request->vol_eos;
            $ativ_cq = $planejamento->vol_max_cq * $ativ_esp;
            $ativ_cq = sprintf("%.1f", $ativ_cq);
            $ativ_eos_nec = $ativ_cq;

            for ($i = 0; $i < count($pedidos_plan); $i++) { 
                // ativ_eos
                $tempo = $planejamento->tempo_exped + $pedidos_plan[$i]->tempo_transp;
                $ativ_eos[$i] = $pedidos_plan[$i]->ativ_dest * exp(M_LN2 * $tempo / $tempo_meia_vida); 
                
                // vol_frasco
                $vol_frasco[$i] = $ativ_eos[$i] / $ativ_esp;
                $vol_frasco[$i] = sprintf("%.1f", $vol_frasco[$i]);
                
                // somatorio ativ_eos_nec
                $ativ_eos_nec += $ativ_eos[$i];
                $ativ_eos[$i] = sprintf("%.1f", $ativ_eos[$i]);
            }

            $ativ_eos_nec = sprintf("%.1f", $ativ_eos_nec * 37);
            $ativ_esp = sprintf("%.1f", $ativ_esp);

            if ($request->action == 'calculate'){
                return redirect()->back()->with(['ativ_eos' => $ativ_eos, 'vol_frasco' => $vol_frasco, 'ativ_eos_nec' => $ativ_eos_nec, 'ativ_esp' => $ativ_esp, 'ativ_cq' => $ativ_cq, 'rend_sintese_real' => $rend_sintese_real, 'fim_sintese' => $fim_sintese->format('H:i:s'), 'hora_saida' => $hora_saida->format('H:i:s')])->withInput();
            }

            else if  ($request->action == 'save'){
                try{
                    DB::beginTransaction();
    
                    $fracionamento = new Fracionamento;
                    $fracionamento->id_usuario = Auth::user()->id;
                    $fracionamento->id_planejamento = $planejamento->id;
                    $fracionamento->data_producao = today();
                    $fracionamento->ativ_eob_calc = $planejamento->ativ_eob;
                    $fracionamento->ativ_eob_real = $request->ativ_eob_real;
                    $fracionamento->fim_sintese = $fim_sintese;
                    $fracionamento->hora_saida = $hora_saida;
                    $fracionamento->ativ_eos_nec = $ativ_eos_nec;
                    $fracionamento->ativ_eos_real = $request->ativ_eos_real;
                    $fracionamento->vol_eos = $request->vol_eos;
                    $fracionamento->ativ_esp = $ativ_esp;
                    $fracionamento->rend_sintese_real = $request->ativ_eob_real;
            
                    $fracionamento->save();
    
                    for ($i=0; $i < count($pedidos_plan); $i++) { 
                        $pedidos_frac[$i] = new Pedido_Frac;
                        $pedidos_frac[$i]->id_pedido = $pedidos_plan[$i]->id_pedido;
                        $pedidos_frac[$i]->id_fracionamento = $fracionamento->id;
                        $pedidos_frac[$i]->qtd_doses_selec = $pedidos_plan[$i]->qtd_doses_selec;
                        $pedidos_frac[$i]->vol_real_frasco = $vol_frasco[$i];
                        
                        $pedidos_frac[$i]->save();
                    }
    
                    DB::commit();
                    return redirect()->route('fracionamentos')->with('alert-success', 'Fracionamento realizado com sucesso.');
                }
                catch (\Exception $exception) {
                    DB::rollback();
                    return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput(); 
                }
            }

    }
}
