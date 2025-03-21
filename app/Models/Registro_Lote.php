<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro_Lote extends Model
{
    public $timestamps = false;

    protected $table = 'registros_lote';

    protected $fillable = [
        'lote',
        'data_fabricacao',
        'lote_agua_enriquecida',
        'id_usuario_lote_agua_enriquecida',
        'pressao_ar_comprimido',
        'pressao_H',
        'pressao_He_refrigeracao',
        'pressao_He_analitico',
        'radiacao_ambiental_lab',
        'id_usuario_verificacao_p3',
        'hora_inicio_irradiacao_agua_enriquecida',
        'hora_final_irradiacao_agua_enriquecida',
        'ativ_teorica_F18',
        'id_usuario_irradiacao_agua_enriquecida',
        'hora_inicio_transferir_F18_sintese',
        'hora_final_transferir_F18_sintese',
        'id_usuario_transferir_F18_sintese',
        'ocorrencias_p3',
        'ocorrencias_horario_p3',
        'id_usuario_ocorrencias_p3',
        'logbook_anexado',
        'logbook_data',
        'logbook_time',
        'id_usuario_logbook',
    ];
}
