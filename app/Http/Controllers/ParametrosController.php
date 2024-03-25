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
}
