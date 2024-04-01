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
        $fracionamento = Fracionamento::where('data_producao', $request->data_producao)->first();

        if ($fracionamento == null)
            return back()->with('alert-dark', 'Não há nenhum Fracionamento para essa data.')->withInput(); 
        

        $pedidos_frac = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, P.QTD_DOSES, C.TEMPO_TRANSP, PF.VOL_REAL_FRASCO
        FROM PEDIDOS P INNER JOIN PEDIDOS_FRAC PF ON (P.ID = PF.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
        WHERE PF.ID_FRACIONAMENTO = ?', [$fracionamento->id]));
        
        return back()->with(['fracionamento' => $fracionamento, 'pedidos_frac' => $pedidos_frac])->withInput();
    }

    public function register(){
        return view('fracionamentos/cadastrar');
    }

    public function store(Request $request){
            $validator = Validator::make(
                $request->all(),
                
                ['data_producao' => 'unique:fracionamentos'],
                
                ['data_producao.unique' => 'Já existe um fracionamento nessa data']
            );

            if ($validator->fails())
                return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     
                
            $planejamento = Planejamento::where('data_producao', $request->data_producao)->first();

            if(!$planejamento)
                return redirect()->back()->with('alert-warning', 'Nenhum planejamento encontrado nessa data.')->withInput();
            
            $pedidos_plan = collect(DB::select('SELECT PP.ID_PEDIDO, C.NOME_FANTASIA, P.QTD_DOSES, C.TEMPO_TRANSP, PP.ATIV_DEST
            FROM PEDIDOS P INNER JOIN PEDIDOS_PLAN PP ON (P.ID = PP.ID_PEDIDO) INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
            WHERE PP.ID_PLANEJAMENTO = ?
            ORDER BY PP.ID_PEDIDO', [$planejamento->id]));

            if ($request->action == 'load'){
                return redirect()->back()->with(['vol_cq' => $planejamento->vol_max_cq, 'eob_calc' => $planejamento->ativ_eob, 'pedidos_plan' => $pedidos_plan])->withInput();
            }

            else{
                // return response()->json('aaa');

                $tempo_meia_vida = 109.7;
                $hora_saida = Carbon::createFromFormat("H:i:s",$planejamento->hora_saida);
                $fim_sintese = Carbon::createFromFormat("H:i:s", date('H:i:s'));

                $ativ_eos_real_mCi = $request->ativ_eos_real / 37;
                $rend_sintese_real = $ativ_eos_real_mCi * 100 / $request->ativ_eob_real;
                $rend_sintese_real = sprintf("%.1f", $rend_sintese_real);
                $ativ_esp = $ativ_eos_real_mCi / $request->vol_eos;
                $ativ_cq = $planejamento->vol_max_cq * $ativ_esp;
                $ativ_cq = sprintf("%.1f", $ativ_cq);
                $ativ_eos_nec = $ativ_cq;

                for ($i = 0; $i < count($pedidos_plan); $i++) { 
                    // ativ_eos
                    $tempo = $fim_sintese->diffInMinutes($hora_saida) + $pedidos_plan[$i]->tempo_transp;
                    $pedidos_plan[$i]->ativ_eos = $pedidos_plan[$i]->ativ_dest * exp(M_LN2 * $tempo / $tempo_meia_vida); 
                    
                    // vol_frasco
                    $pedidos_plan[$i]->vol_frasco = $pedidos_plan[$i]->ativ_eos / $ativ_esp;
                    $pedidos_plan[$i]->vol_frasco = sprintf("%.1f", $pedidos_plan[$i]->vol_frasco);
                    
                    // somatorio ativ_eos_nec
                    $ativ_eos_nec += $pedidos_plan[$i]->ativ_eos;
                    $pedidos_plan[$i]->ativ_eos = sprintf("%.1f", $pedidos_plan[$i]->ativ_eos);
                }

                $ativ_eos_nec = sprintf("%.1f", $ativ_eos_nec * 37);
                $ativ_esp = sprintf("%.1f", $ativ_esp);

                if ($request->action == 'calculate'){
                    return redirect()->back()->with(['vol_cq' => $planejamento->vol_max_cq, 'eob_calc' => $planejamento->ativ_eob, 'pedidos_plan' => $pedidos_plan, 'ativ_eos_nec' => $ativ_eos_nec, 'ativ_esp' => $ativ_esp, 'ativ_cq' => $ativ_cq, 'rend_sintese_real' => $rend_sintese_real, 'fim_sintese' => $fim_sintese->format('H:i:s')])->withInput();
                }

                else if  ($request->action == 'save'){
                    try{
                        DB::beginTransaction();
        
                        $fracionamento = new Fracionamento;
                        $fracionamento->id_usuario = Auth::user()->id;
                        $fracionamento->data_producao = $request->data_producao;
                        $fracionamento->ativ_eob_calc = $planejamento->ativ_eob;
                        $fracionamento->ativ_eob_real = $request->ativ_eob_real;
                        $fracionamento->fim_sintese = $fim_sintese;
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
                            $pedidos_frac[$i]->vol_real_frasco = $pedidos_plan[$i]->vol_frasco;
                            
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
}
