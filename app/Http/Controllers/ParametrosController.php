<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;

class ParametrosController extends Controller
{
    public function index(){
        $parametros = Parametro::first();
        $columns = ['Atividade p/ Dose', 'Tempo entre Exames', 'Volume p/ C.Q.', 'Tempo de Expedição', 'Rend. Típico do Cíclotron', 'Corrente Alvo', 'Rend. Síntese', 'Tempo da Síntese', 'Volume EOS', 'Horário de Saída',];
        $indexes = ['ativ_dose', 'tempo_exames', 'vol_max_cq', 'tempo_exped', 'rend_tip_ciclotron', 'corrente_alvo', 'rend_sintese', 'tempo_sintese', 'vol_eos', 'hora_saida',];
        return view('parametros/visualizar', ['parametros' => $parametros, 'indexes' => $indexes, 'columns' => $columns]);
    }

    public function update(Request $request){
        $parametros = Parametro::all()[0];
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
        return redirect()->route('parametros')->with('alert-success', 'Parâmetros editados com sucesso');
    }
}
