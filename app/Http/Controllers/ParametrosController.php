<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ParametrosController extends Controller
{
    public function index(){
        $parametros = Parametro::first();
        $columns = ['Atividade p/ Dose', 'Tempo entre Exames', 'Volume p/ C.Q.', 'Tempo de Expedição', 'Rend. Típico do Cíclotron', 'Corrente Alvo', 'Rend. Síntese', 'Tempo da Síntese', 'Volume EOS', 'Horário de Saída',];
        $indexes = ['ativ_dose', 'tempo_exames', 'vol_max_cq', 'tempo_exped', 'rend_tip_ciclotron', 'corrente_alvo', 'rend_sintese', 'tempo_sintese', 'vol_eos', 'hora_saida',];
        return view('parametros/visualizar', ['parametros' => $parametros, 'indexes' => $indexes, 'columns' => $columns]);
    }

    public function update(Request $request){
        try{
            DB::beginTransaction();
            $parametros = Parametro::all()[0];

            $parametrosAntes = $parametros->toArray();

            $parametros->update([
                'ativ_dose' => $request->ativ_dose,
                'tempo_exames' => $request->tempo_exames,
                'vol_max_cq' => $request->vol_max_cq,
                'tempo_exped' => $request->tempo_exped,
                'rend_tip_ciclotron' => $request->rend_tip_ciclotron,
                'corrente_alvo' => $request->corrente_alvo,
                'rend_sintese' => $request->rend_sintese,
                'tempo_sintese' => $request->tempo_sintese,
                'vol_eos' => $request->vol_eos,
                'hora_saida' => $request->hora_saida,
            ]);

            $parametrosDepois = $parametros->refresh()->toArray();

            // ADICIONANDO LOG
            if (array_diff($parametrosAntes, $parametrosDepois) != null){
                $log = new Log();
    
                $log->id_user = Auth::user()->id;
                $log->id_acao = Acao::where('descricao', 'Editar Parâmetros')->first()["id"];
                $log->tipo = "Info";
                $log->data_hora = now();
                $log->descricao = 
                    "Parâmetros editados:\n";
    
                    foreach ($parametrosDepois as $campo => $valor) {
                        if ($valor != ($parametrosAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($parametrosAntes[$campo] === null || $parametrosAntes[$campo] === '' ? '(não informado)' : $parametrosAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }
    
                $log->save();
            }
    
                DB::commit();
                return redirect()->route('parametros')->with('alert-success', 'Parâmetros editados com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na atualização no banco de dados: ' . $exception->getMessage())->withInput();
        }
        }
}
