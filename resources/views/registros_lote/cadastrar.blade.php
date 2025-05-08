<x-app-layout>
    <div class="w-100 py-16">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
            <div class="container justify-content-center">
                <div class="pb-4 m-5 text-gray-900 text-center h3">
                    {{ 'Novo Registro de Lote' }}
                </div>

                <form action="{{ route('registros_lote.store') }}" method="POST">
                    @csrf

                    <!-- Páginas do Formulário -->
                    <div id="pagina1">
                        <h4 class="mt-4 mb-5">1. Dados do Lote</h4>
                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Lote: </th>
                                    <td>
                                        <input type="text" class="form-control" id="lote" name="lote" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Data de Fabricação: </th>
                                    <td>
                                        <input type="date" class="form-control" id="data_fabricacao" name="data_fabricacao" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="pagina2" style="display: none;">
                        <h4  class="mt-4 mb-5">2. Irradiação</h4>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Lote da água enriquecida <sup>18</sup>O 95%:  </th>
                                    <td><input type="text" class="form-control" id="lote_agua_enriquecida" name="lote_agua_enriquecida"></td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">   
                                        <select class="form-select" id="id_usuario_lote_agua_enriquecida" name="id_usuario_lote_agua_enriquecida">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select> 
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <table class="table table-bordered mt-5">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Verificar </th>
                                    <th class="table-light text-center text-dark" scope="col"> Especificações </th>
                                    <th class="table-light text-center text-dark" scope="col"> Medida </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Pressão de ar comprimido (Medição na saída 
                                        do Compressor): </th>
                                    <td> 8 - 10 bar </td>
                                    <td>
                                        <input type="number" min="0" step="0.1" class="form-control" id="pressao_ar_comprimido" name="pressao_ar_comprimido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressão de Hidrogênio 6.0:  </th>
                                    <td> 1 - 3 bar </td>
                                    <td>
                                        <input type="number" min="0" step="0.1" class="form-control" id="pressao_H" name="pressao_H">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressão de Helio de Refrigeração 4.5: </th>
                                    <td> 0,2 - 1 bar </td>
                                    <td>
                                        <input type="number" min="0" step="0.1" class="form-control" id="pressao_He_refrigeracao" name="pressao_He_refrigeracao">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressão de Helio 5.0 analítico: </th>
                                    <td> 1,5 - 3 bar </td>
                                    <td>
                                        <input type="number" min="0" step="0.1" class="form-control" id="pressao_He_analitico" name="pressao_He_analitico">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Radiação ambiental no Laboratório de 
                                        Produção: </th>
                                    <td> < 5 µSv/h </td>
                                    <td>
                                        <input type="number" min="0" step="0.1" class="form-control" id="radiacao_ambiental_lab" name="radiacao_ambiental_lab">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_p3" name="id_usuario_verificacao_p3">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>    
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <h6  class="mb-4 mt-5">2.1 Realizar irradiação da água enriquecida em <sup>18</sup>O 95%</h6>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Início (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_inicio_irradiacao_agua_enriquecida" name="hora_inicio_irradiacao_agua_enriquecida">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Final (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_final_irradiacao_agua_enriquecida" name="hora_final_irradiacao_agua_enriquecida">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Atividade Teórica do <sup>18</sup>F (mCi): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ativ_teorica_F18" name="ativ_teorica_F18">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Realizado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_irradiacao_agua_enriquecida" name="id_usuario_irradiacao_agua_enriquecida">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <h6  class="mb-4 mt-5">2.2  Transferir o <sup>18</sup>F para o Módulo de Síntese</h6>
                        
                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Início (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_inicio_transferir_F18_sintese" name="hora_inicio_transferir_F18_sintese">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Final (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_final_transferir_F18_sintese" name="hora_final_transferir_F18_sintese">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Realizado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_transferir_F18_sintese" name="id_usuario_transferir_F18_sintese">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p3" name="ocorrencias_p3" maxlength="260"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Horário: </th>
                                    <td>
                                        <input type="time" class="form-control" id="ocorrencias_horario_p3" name="ocorrencias_horario_p3">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_ocorrencias_p3" name="id_usuario_ocorrencias_p3">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> O Logbook foi anexado? </th>
                                    <td>
                                        <select class="form-select" id="logbook_anexado" name="logbook_anexado">
                                            <option selected hidden></option>
                                            <option value="0"> Nâo </option>
                                            <option value="1"> Sim </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Data: </th>
                                    <td>
                                        <input type="date" class="form-control" id="logbook_data" name="logbook_data">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Hora: </th>
                                    <td>
                                        <input type="time" class="form-control" id="logbook_time" name="logbook_time">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_logbook" name="id_usuario_logbook">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <div id="pagina3" style="display: none;">
                        <h4 class="mt-4">3. Síntese</h4>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Módulo de Síntese: </th>
                                    <td>
                                        <select class="form-select" id="modulo_sintese" name="modulo_sintese">
                                            <option selected hidden></option>
                                            <option value="0"> 077 </option>
                                            <option value="1"> 078 </option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <h6  class="mb-4 mt-5">3.1 Ações prévias à síntese </h6>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Separar </th>
                                    <th class="table-light text-center text-dark" scope="col"> Qtd. </th>
                                    <th class="table-light text-center text-dark" scope="col"> Lote </th>
                                    <th class="table-light text-center text-dark" scope="col"> Validade </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Kryptofix 222 (K<sub>2</sub>CO<sub>3</sub>): </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="kryptofix222_lote" name="kryptofix222_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="kryptofix222_data_validade" name="kryptofix222_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Triflato de manose: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="triflato_manose_lote" name="triflato_manose_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="triflato_manose_data_validade" name="triflato_manose_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Hidróxido de sódio (NaOH): </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="hidroxido_sodio_lote" name="hidroxido_sodio_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="hidroxido_sodio_data_validade" name="hidroxido_sodio_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Água para injetáveis: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="agua_injetaveis_lote" name="agua_injetaveis_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="agua_injetaveis_data_validade" name="agua_injetaveis_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Acetonitrila anidra: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="acetronitrila_anidra_lote" name="acetronitrila_anidra_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="acetronitrila_anidra_data_validade" name="acetronitrila_anidra_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> IFP - Synthera: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="ifp_synthera_lote" name="ifp_synthera_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="ifp_synthera_data_validade" name="ifp_synthera_data_validade">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered mt-5">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Colunas/Condicionantes </th>
                                    <th class="table-light text-center text-dark" scope="col"> Qtd. </th>
                                    <th class="table-light text-center text-dark" scope="col"> Lote </th>
                                    <th class="table-light text-center text-dark" scope="col"> Validade </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Sep-Pak Light Accell Plus QMA: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="sep_pak_lote" name="sep_pak_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="sep_pak_data_validade" name="sep_pak_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Coluna SCX: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="coluna_scx_lote" name="coluna_scx_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="coluna_scx_data_validade" name="coluna_scx_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Coluna C18: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="coluna_c18_lote" name="coluna_c18_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="coluna_c18_data_validade" name="coluna_c18_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Coluna Alumina: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="coluna_alumina_lote" name="coluna_alumina_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="coluna_alumina_data_validade" name="coluna_alumina_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Seringa descartável de 3 ml: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="seringa_3ml_lote" name="seringa_3ml_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="seringa_3ml_data_validade" name="seringa_3ml_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Agulha 0,5 x 25 mm: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="agulha_05x25_lote" name="agulha_05x25_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="agulha_05x25_data_validade" name="agulha_05x25_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Água injetável (25ml) em Seringa: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="agua_injetavel_seringa_lote" name="agua_injetavel_seringa_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="agua_injetavel_seringa_data_validade" name="agua_injetavel_seringa_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Etanol (10ml) em Seringa: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="etanol_seringa_lote" name="etanol_seringa_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="etanol_seringa_data_validade" name="etanol_seringa_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> NaHCO<sub>3</sub> a 8,4% (5ml) em Seringa: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="NaHCO3_seringa_lote" name="NaHCO3_seringa_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="NaHCO3_seringa_data_validade" name="NaHCO3_seringa_data_validade">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Separado e Registrado por: 
                                            <select class="form-select ms-2" id="id_usuario_separado_registrado_p4" name="id_usuario_separado_registrado_p4">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Data:
                                            <input type="date" class="ms-2 form-control" id="data_separado_registrado_p4" name="data_separado_registrado_p4">
                                        </div> 
                                    </th>
                                </tr>
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Recebido e Conferido por:
                                            <select class="form-select ms-2" id="id_usuario_recebido_conferido_p4" name="id_usuario_recebido_conferido_p4">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center"> 
                                            Data: 
                                            <input type="date" class="form-control ms-2" id="data_recebido_conferido_p4" name="data_recebido_conferido_p4">
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    
                    </div>

                    <div id="pagina4" style="display: none;">
                        <h6  class="mb-4 mt-5">3.2 Realizar montagem do KIT SYNTHERA </h6>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Início (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_inicio_montagem_kit_synthera" name="hora_inicio_montagem_kit_synthera">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Final (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_final_montagem_kit_synthera" name="hora_final_montagem_kit_synthera">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_execucao_montagem_kit_synthera" name="id_usuario_execucao_montagem_kit_synthera">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_montagem_kit_synthera" name="id_usuario_verificacao_montagem_kit_synthera">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered mt-5">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Registrar </th>
                                    <th class="table-light text-center text-dark" scope="col"> Especificações </th>
                                    <th class="table-light text-center text-dark" scope="col"> Medida </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Temperatura do Laboratório de Produção: </th>
                                    <td> 15 a 25ºC </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="temperatura_lab_producao" name="temperatura_lab_producao">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Umidade do Laboratório de Produção:  </th>
                                    <td> 30 a 70% UR </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="umidade_lab_producao" name="umidade_lab_producao">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_p5" name="id_usuario_verificacao_p5">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>    
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <h6  class="mb-4 mt-5">3.3 Check-list para síntese </h6>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Ações </th>
                                    <th class="table-light text-center text-dark" scope="col"> Verificação </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Limpeza da Célula: </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="limpeza_celula">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Verificar volume de H<sub>2</sub><sup>18</sup>O no frasco de recuperação </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verif_volume_H218O">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Verificar frasco de rejeitos </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verif_frasco_rejeitos">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Verificar bolsa de ar </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verif_bolsa_ar">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Abrir válvula de Ar comprimido (7-7,5 bar) </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="abrir_valvula_ar_comprimido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Abrir válvula de Nitrogênio (17,5 psi) </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="abrir_valvula_nitrogenio">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Verificar posicionamento dos capilares no frasco em "V" </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verif_pos_capilares">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Ligar Cx. Controle do Synthera </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="ligar_controle_synthera">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Ligar NoteBook do Synthera </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="ligar_notebook_synthera">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Iniciar programa MPB </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="iniciar_programa_mpb">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Retirar o IFP "usado" (se ainda presente) </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="retirar_ifp_usado">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Inserir o IFP no Synthera, pressionar os dois botões LOAD </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="inserir_ifp_synthera">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Inserir o IFP no Synthera, pressionar os dois botões LOAD </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="inserir_ifp_synthera">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Conectar a linha de transferência do produto final ao THEODORICO </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="conectar_theodorico">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Iniciar auto-teste pressionando o botão START (no PC) </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="iniciar_auto_teste">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Efetuar diluição do triflato de manose </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="efetuar_diluicao_triflato_manose">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Remover BLOCO VERMELHO de Segurança das agulhas e prender o capilar </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="remover_bloco_vermelho">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Fechar as portas da BBS (acionar "LOCKED / VENTILATION") </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="fechar_portas_bbs">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Ao chegar o radioisótopo, pressionar o botão "START"(verde) na caixa de controle  </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_start">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="2" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_acoes" name="id_usuario_verificacao_acoes">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <div id="pagina5" style="display: none;">
                        <h6 class="mt-5 border border-black py-1 fw-bold text-center mx-auto" style="max-width: 70%;">
                            <span class="fw-normal">ATENÇÃO!</span> Caso algum problema seja verificado, checar medidas para resolução, no POP correspondente à atividade, antes de seguir para próxima ação.
                        </h6>

                        <h6 class="my-4 py-1 text-center mx-auto" style="max-width: 70%;">
                            *Verificar, no calibrador de dose, a Atividade do <sup>18</sup>F que chega ao módulo de síntese: Atividade de chegada menos a Atividade residual. 
                        </h6>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Atividade de CHEGADA do <sup>18</sup>F (mCi): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ativ_chegada_18F" name="ativ_chegada_18F">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Atividade RESIDUAL do <sup>18</sup> (mCi): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ativ_residual_18F" name="ativ_residual_18F">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Atividade no MÓDULO DE SÍNTESE (mCi): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ativ_modulo_sintese" name="ativ_modulo_sintese">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Atividade no MODÚLO DE FRACIONAMENTO (mCi): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ativ_modulo_fracionamento" name="ativ_modulo_fracionamento">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Início da Síntese (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_inicio_sintese" name="hora_inicio_sintese">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Final da Síntese (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_final_sintese" name="hora_final_sintese">
                                    </td>
                                </tr>
                                <tr>
                                    <th> RENDIMENTO DA SÍNTESE (%) : </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="rendimento_sintese" name="rendimento_sintese">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_execucao_p6" name="id_usuario_execucao_p6">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_p6" name="id_usuario_verificacao_p6">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p6" name="ocorrencias_p6" maxlength="520"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Horário: </th>
                                    <td>
                                        <input type="time" class="form-control" id="ocorrencias_horario_p6" name="ocorrencias_horario_p6">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_execucao_ocorrencias_p6" name="id_usuario_execucao_ocorrencias_p6">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p6" name="id_usuario_verificacao_ocorrencias_p6">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina6" style="display: none;">
                        <h4 class="mt-4">4. Fracionamento</h4>

                        <h6 class="my-4 py-1 text-center mx-auto" style="max-width: 70%;">
                            Sistema de Fracionamento → THEODORICO - CÓDIGO - PROD - SDF - 001 
                        </h6>

                        <h6  class="mb-4 mt-5">4.1 Ações anteriores ao fracionamento </h6>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Separar </th>
                                    <th class="table-light text-center text-dark" scope="col"> Qtd. </th>
                                    <th class="table-light text-center text-dark" scope="col"> Lote </th>
                                    <th class="table-light text-center text-dark" scope="col"> Validade </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Kit de fracionamento (parte 1): </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="kit_fracionamento_1_lote" name="kit_fracionamento_1_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="kit_fracionamento_1_data_validade" name="kit_fracionamento_1_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Kit de fracionamento (parte 2): </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="kit_fracionamento_2_lote" name="kit_fracionamento_2_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="kit_fracionamento_2_data_validade" name="kit_fracionamento_2_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Filtro Millex GS: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="filtro_millex_gs_lote" name="filtro_millex_gs_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="filtro_millex_gs_data_validade" name="filtro_millex_gs_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Filtro Millex GV: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="filtro_millex_gv_lote" name="filtro_millex_gv_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="filtro_millex_gv_data_validade" name="filtro_millex_gv_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Soro fisiológico (NaCl 0,9%): </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="soro_fisiologico_lote" name="soro_fisiologico_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="soro_fisiologico_data_validade" name="soro_fisiologico_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Agulha 0,9 x 40: </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="agulha_09x40_lote" name="agulha_09x40_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="agulha_09x40_data_validade" name="agulha_09x40_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Frascos 15mL estéreis e apirogênicos: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="frascos_15ml_qtd" name="frascos_15ml_qtd">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="frascos_15ml_lote" name="frascos_15ml_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="frascos_15ml_data_validade" name="frascos_15ml_data_validade">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Frasco "Bulk" estéril e apirogênico (30ml): </th>
                                    <td> 01 </td>
                                    <td>
                                        <input type="text" class="form-control" id="frascos_bulk_lote" name="frascos_bulk_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="frascos_bulk_data_validade" name="frascos_bulk_data_validade">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Separado e Registrado por: 
                                            <select class="form-select ms-2" id="id_usuario_separado_registrado_p7" name="id_usuario_separado_registrado_p7">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Data:
                                            <input type="date" class="ms-2 form-control" id="data_separado_registrado_p7" name="data_separado_registrado_p7">
                                        </div> 
                                    </th>
                                </tr>
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Recebido e Conferido por:
                                            <select class="form-select ms-2" id="id_usuario_recebido_conferido_p7" name="id_usuario_recebido_conferido_p7">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center"> 
                                            Data: 
                                            <input type="date" class="form-control ms-2" id="data_recebido_conferido_p7" name="data_recebido_conferido_p7">
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Ações </th>
                                    <th class="table-light text-center text-dark" scope="col"> Verificação </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Ligar Theodorico, iniciar programa Movicon: </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="ligar_theodorico">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Colocar castelo de chumbo no DWS e marcar sua posição no software </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="colocar_castelo_chumbo_dws">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressionar o botão "Park" no painel Movicon </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_botao_park">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressionar a botão "Pinch Open" no painel Movicon </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_botao_pinch_open">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Retirar kit usado do Theodorico </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="retirar_kit_usado">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Realizar limpeza do Theodorico </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="realizar_limpeza_theodorico">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Conectar capilares do Synthera ao "Bulk" usando agulhas </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="conectar_capilares_synthera_bulk">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Conectar kit de fracionamento (parte 1) </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="conectar_kit_fracionamento_1">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Fechar bomba peristáltica </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="fechar_bomba_peristaltica">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressionar a botão "Pinch Close" no painel Movicon </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_botao_pinch_close">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Conectar kit de fracionamento (parte 2) </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="conectar_kit_fracionamento_2">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Prender os capilares nas paredes do Theodorico </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="prender_capilares_parede_theodorico">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Conectar os filtros "Millex GS" entre as partes 1 e 2 do kit de fracionamento  </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="conectar_filtros_millex_gs">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Verificar se todas as linhas estão corretamente conectadas </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verificar_linhas_conectadas">
                                    </td>
                                </tr>
                                <tr>
                                    <th> IVerificar se todas as conexões dos capilares e agulhas estão bem ajustadas </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verificar_conexoes_capilares">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Verificar se a agulha sucção do radiofármaco toca o fundo do "Bulk" </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="verificar_agulha_succao">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Fechar porta. Pressionar "Inflate" e "Ventilation" </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="fechar_porta">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Programar o fracionamento no software </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="programar_fracionamento_software">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Imprimir etiquetas dos frascos e colá-las </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="imprimir_etiqueta_frascos">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Alimentar antecâmara com frascos. Rótulo virado para fora </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="alimentar_antecamara_frascos">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Marcar, no software, a posição dos frascos </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="marcar_posicao_frascos">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Ao chegar radiofármaco, pressionar botão "From SYNT" </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_botao_from_synt">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressionar botão "Bulk Dilution", escrever o volume e confirmar </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_botao_bulk_dilution">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Pressionar botão "Start" </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="pressionar_botao_start">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="2" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificado_p8" name="id_usuario_verificado_p8">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>

                    <div id="pagina7" style="display: none;">
                        <h6 class="my-4 py-1 text-center mx-auto" style="max-width: 70%;">
                            * Anotar a atividade de FDG 18F que chega ao módulo de fracionamento.
                        </h6>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Atividade de FDG <sup>18</sup>F (mCi): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="atividade_fdg_18f" name="atividade_fdg_18f">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Volume de Soro Fisiológico adicionado (ml): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="volume_soro_fisiologico" name="volume_soro_fisiologico">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Imprimir e anexar relatório de produção: </th>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="imprimir_anexar_relatorio_producao">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Início do Fracionamento (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_inicio_p8" name="hora_inicio_p8">
                                    </td>
                                </tr>
                                    <th> Final do Fracionamento (h): </th>
                                    <td>
                                        <input type="time" class="form-control" id="hora_final_p8" name="hora_final_p8">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Fracionamento Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_fracionamento_executado" name="id_usuario_fracionamento_executado">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>   
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <h6 class="my-5 text-center mx-auto" style="max-width: 70%;">
                            * Divide-se a atividade de FDG-18 que chegou ao módulo de fracionamento pela Atividade do <sup>18</sup>F que seguiu para módulo de síntese. O resultado deve ser multiplicado por 100. 
                        </h6>

                        <h6 class="mt-5 border border-black py-1 fw-bold text-center mx-auto" style="max-width: 70%;">
                            ATENÇÃO! Caso algum problema seja verificado, checar medidas para resolução no POP correspondente à atividade, antes de seguir para próxima ação.
                            <br><br>
                            Ao final de cada envase, colar as etiquetas correspondentes, colocar o medicamento dos clientes no pass through de Expedição e as amostras destinadas à realização de análises de Controle de Qualidade devem ser colocadas no pass through do Controle de Qualidade Microbiológico. 
                        </h6>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p9" name="ocorrencias_p9" maxlength="820"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Horário: </th>
                                    <td>
                                        <input type="time" class="form-control" id="ocorrencias_horario_p9" name="ocorrencias_horario_p9">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_execucao_ocorrencias_p9" name="id_usuario_execucao_ocorrencias_p9">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p9" name="id_usuario_verificacao_ocorrencias_p9">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina8" style="display: none;">
                        <h4 class="mt-4">5. Embalagem e Expedição </h4>

                        <h6  class="mb-4 mt-5">5.1 Embalagem </h6>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Separar </th>
                                    <th class="table-light text-center text-dark" scope="col"> Qtd. </th>
                                    <th class="table-light text-center text-dark" scope="col"> Separado </th>
                                    <th class="table-light text-center text-dark" scope="col"> Conferido </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Embalagem (balde): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="embalagem_balde_qtd" name="embalagem_balde_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="embalagem_balde_separado" name="embalagem_balde_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="embalagem_balde_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Embalagem (case/maleta): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="embalagem_case_qtd" name="embalagem_case_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="embalagem_case_separado" name="embalagem_case_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="embalagem_case_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Etiquetas IT: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="etiquetas_it_qtd" name="etiquetas_it_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="etiquetas_it_separado" name="etiquetas_it_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="etiquetas_it_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Bulas FDG <sup>18</sup>F: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="bulas_fdg_qtd" name="bulas_fdg_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="bulas_fdg_separado" name="bulas_fdg_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="bulas_fdg_conferido">
                                    </td>
                                </tr>
                                
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Separado por: 
                                            <select class="form-select ms-2" id="id_usuario_separado_embalagem_p10" name="id_usuario_separado_embalagem_p10">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Hora:
                                            <input type="time" class="ms-2 form-control" id="horario_separado_embalagem_p10" name="horario_separado_embalagem_p10">
                                        </div> 
                                    </th>
                                </tr>
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Conferido por:
                                            <select class="form-select ms-2" id="id_usuario_conferido_embalagem_p10" name="id_usuario_conferido_embalagem_p10">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center"> 
                                            Hora: 
                                            <input type="time" class="form-control ms-2" id="horario_conferido_embalagem_p10" name="horario_conferido_embalagem_p10">
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>

                        <h6  class="mb-4 mt-5">5.2 Expedição </h6>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Separar </th>
                                    <th class="table-light text-center text-dark" scope="col"> Qtd. </th>
                                    <th class="table-light text-center text-dark" scope="col"> Separado </th>
                                    <th class="table-light text-center text-dark" scope="col"> Conferido </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th> Declaração do Expedidor: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="decl_exped_qtd" name="decl_exped_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="decl_exped_separado" name="decl_exped_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="decl_exped_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Ficha(s) de Emergência: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ficha_emerg_qtd" name="ficha_emerg_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ficha_emerg_separado" name="ficha_emerg_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="ficha_emerg_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Nota(s) Fiscal(is): </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="nota_fiscal_qtd" name="nota_fiscal_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="nota_fiscal_separado" name="nota_fiscal_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="nota_fiscal_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Termo de Doação: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="termo_doacao_qtd" name="termo_doacao_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="termo_doacao_separado" name="termo_doacao_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="termo_doacao_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Identificação do Veículo: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ident_veiculo_qtd" name="ident_veiculo_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="ident_veiculo_separado" name="ident_veiculo_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="ident_veiculo_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Formulário TAM: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="form_tam_qtd" name="form_tam_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="form_tam_separado" name="form_tam_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="form_tam_conferido">
                                    </td>
                                </tr>
                                <tr>
                                    <th> Formulário IATA: </th>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="form_iata_qtd" name="form_iata_qtd">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control" id="form_iata_separado" name="form_iata_separado">
                                    </td>
                                    <td>
                                        <input class="form-check-input me-2" type="checkbox" name="form_iata_conferido">
                                    </td>
                                </tr>
                                
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Separado por: 
                                            <select class="form-select ms-2" id="id_usuario_separado_expedicao_p10" name="id_usuario_separado_expedicao_p10">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Hora:
                                            <input type="time" class="ms-2 form-control" id="horario_separado_expedicao_p10" name="horario_separado_expedicao_p10">
                                        </div> 
                                    </th>
                                </tr>
                                <tr>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            Conferido por:
                                            <select class="form-select ms-2" id="id_usuario_conferido_expedicao_p10" name="id_usuario_conferido_expedicao_p10">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th class="table-light" colspan="2" scope="col">
                                        <div class="flex align-items-center"> 
                                            Hora: 
                                            <input type="time" class="form-control ms-2" id="horario_conferido_expedicao_p10" name="horario_conferido_expedicao_p10">
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    
                        <h6  class="mb-4 mt-5">5.3 Término do Procedimento de Embalagem/Expedição </h6>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th> Horário: </th>
                                    <td>
                                        <input type="time" class="form-control" id="horario_final_emb_exped" name="horario_final_emb_exped">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_execucao_p10" name="id_usuario_execucao_p10">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_p10" name="id_usuario_verificacao_p10">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <h6  class="mb-4 mt-5">5.4 Ocorrências no procedimento de Embalagem/Expedição </h6>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p10" name="ocorrencias_p10" maxlength="200"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Horário: </th>
                                    <td>
                                        <input type="time" class="form-control" id="ocorrencias_horario_p10" name="ocorrencias_horario_p10">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Executado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_execucao_ocorrencias_p10" name="id_usuario_execucao_ocorrencias_p10">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p10" name="id_usuario_verificacao_ocorrencias_p10">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina9" style="display: none;">
                        <h4 class="mb-4 mt-4">6. Registro de Análises de Controle de Qualidade Físico-Químico  </h4>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Ensaio </th>
                                    <th class="table-light text-center text-dark" scope="col"colspan="2"> Especificação </th>
                                    <th class="table-light text-center text-dark" scope="col"> Código do Equipamento </th>
                                    <th class="table-light text-center text-dark" scope="col"> Resultado </th>
                                    <th class="table-light text-center text-dark" scope="col"> Data </th>
                                    <th class="table-light text-center text-dark" scope="col"> Analista </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th class="table-light"> Aspecto da solução: </th>
                                    <td colspan="2"> <sup>a</sup>Análise visual Límpida, incolor ou ligeiramente amarelada. </td>
                                    <td> N/A </td>
                                    <td>
                                        <input type="text" class="form-control" id="aspecto_resultado" name="aspecto_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="aspecto_data" name="aspecto_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_aspecto" name="id_usuario_aspecto">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" rowspan="2"> pH: </th>
                                    <td rowspan="2"> <sup>a</sup>Papel indicador </td>
                                    <td> pH do Padrão 7,0 </td>
                                    <td rowspan="2"> N/A </td>
                                    <td>
                                        <input type="number" step="0.1" class="form-control" id="ph_1_resultado" name="ph_1_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="ph_1_data" name="ph_1_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_ph_1 name="id_usuario_ph_1>
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td> pH do FDG-<sup>18</sup>F 4,5 - 8,5 </td>
                                    <td>
                                        <input type="number" step="0.1" class="form-control" id="ph_2_resultado" name="ph_2_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="ph_2_data" name="ph_2_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_ph_2" name="id_usuario_ph_2">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light" rowspan="2"> Pureza Radionuclídica: </th>
                                    <td rowspan="2"> <sup>a</sup>Detector de germânio </td>
                                    <td> Energia dos fótons em 511 keV com possível pico em 1022 keV </td>
                                    <td rowspan="2"> CQFQ-DGE-001 </td>
                                    <td>
                                        <input type="text" class="form-control" id="pureza_radionuclidica_1_resultado" name="pureza_radionuclidica_1_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="pureza_radionuclidica_1_data" name="pureza_radionuclidica_1_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_pureza_radionuclidica_1" name="id_usuario_pureza_radionuclidica_1">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td> Observação após 24 h. Impurezas ≤ 0,1% </td>
                                    <td>
                                        <input type="text" class="form-control" id="pureza_radionuclidica_2_resultado" name="pureza_radionuclidica_2_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="pureza_radionuclidica_2_data" name="pureza_radionuclidica_2_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_pureza_radionuclidica_2" name="id_usuario_pureza_radionuclidica_2">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light"> Tempo de Meia Vida: </th>
                                    <td colspan="2"> <sup>a</sup>Activímetro <br> 105 - 115 minutos </td>
                                    <td> CQFQ-ACT-001 </td>
                                    <td>
                                        <input type="text" class="form-control" id="meia_vida_resultado" name="meia_vida_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="meia_vida_data" name="meia_vida_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_meia_vida" name="id_usuario_meia_vida">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light"> Solventes residuais: </th>
                                    <td colspan="2"> <sup>b</sup> Cromatógrafo gasoso <br> Etanol ≤ 5000 µg/ml <br> Acetonitrila ≤ 400 µg/ml </td>
                                    <td> CQFQ-CGS-001  </td>
                                    <td>
                                        <input type="text" class="form-control" id="solventes_resultado" name="solventes_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="solventes_data" name="solventes_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_solventes" name="id_usuario_solventes">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <label class="text-sm"> 
                            <sup>a</sup> British Pharmacopeia. Vol. III, 2009.
                            <br>
                            <sup>b</sup> USP Pharmacopeia. 31ª Ed. 2007.
                        </label>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p11" name="ocorrencias_p11" maxlength="220"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p11" name="id_usuario_verificacao_ocorrencias_p11">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina10" style="display: none;">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Ensaio </th>
                                    <th class="table-light text-center text-dark" scope="col"> Especificação </th>
                                    <th class="table-light text-center text-dark" scope="col"> Código do Equipamento </th>
                                    <th class="table-light text-center text-dark" scope="col"> Resultado </th>
                                    <th class="table-light text-center text-dark" scope="col"> Data </th>
                                    <th class="table-light text-center text-dark" scope="col"> Analista </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th rowspan="2" class="table-light"> Pureza Radioquímica: </th>
                                    <td> <sup>a</sup> HPLC <br> Tr <sup>18</sup>FDM / Tr <sup>18</sup>FDG ≅ 0,90 <br> <sup>18</sup>FDG+<sup>18</sup>FDM ≥ 95% <br> radioatividade total <sup>18</sup>FDM ≤ 10% radioatividade total </td>
                                    <td>
                                        <select class="form-select" id="pureza_radioquimica_a_codigo" name="pureza_radioquimica_a_codigo">
                                                <option selected disabled>Selecione o código</option>
                                                <option value="0">CQFQ-CCF-001</option>
                                                <option value="1">CQFQ-CCF-002</option>
                                            </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="pureza_radioquimica_a_resultado" name="pureza_radioquimica_a_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="pureza_radioquimica_a_data" name="pureza_radioquimica_a_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_pureza_radioquimica_a" name="id_usuario_pureza_radioquimica_a">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td> <sup>b</sup>TLC <br> RF <sub>[FDG+FDM]</sub> = RF<sub>Padrão</sub> <br> 0,40 ≤ RF <sub>[FDG+FDM]</sub> ≤ 0,60 <br> <sup>18</sup>FDG <sup>18</sup>FDM ≥ 90% radioatividade total </td>
                                    <td> CQFQ-CCF-001 </td>
                                    <td>
                                        <input type="text" class="form-control" id="pureza_radioquimica_b_resultado" name="pureza_radioquimica_b_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="pureza_radioquimica_b_data" name="pureza_radioquimica_b_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_pureza_radioquimica_b" name="id_usuario_pureza_radioquimica_b">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table-light"> Pureza Química: </th>
                                    <td> <sup>b</sup>Kryptofix-TLC - A intensidade da mancha referente à amostra de FDG deve ser menor que a mancha da solução padrão (≤ 0,50 µg/ml) </td>
                                    <td> N/A </td>
                                    <td>
                                        <input type="text" class="form-control" id="pureza_quimica_resultado" name="pureza_quimica_resultado">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="pureza_quimica_data" name="pureza_quimica_data">
                                    </td>
                                    <td colspan="2" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_pureza_quimica" name="id_usuario_pureza_quimica">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>

                        <label class="text-sm"> 
                            <sup>a</sup> British Pharmacopeia. Vol. III, 2009.
                            <br>
                            <sup>b</sup> USP Pharmacopeia. 31ª Ed. 2007.
                        </label>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p12" name="ocorrencias_p12" maxlength="220"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p12" name="id_usuario_verificacao_ocorrencias_p12">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Aprovado? </th>
                                    <td>
                                        <select class="form-select" id="aprovacao_fisico_quimico" name="aprovacao_fisico_quimico">
                                            <option selected hidden></option>
                                            <option value="0"> Nâo </option>
                                            <option value="1"> Sim </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Data: </th>
                                    <td>
                                        <input type="date" class="form-control" id="data_aprovacao_fisico_quimico" name="data_aprovacao_fisico_quimico">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Farmacêutico Responsável: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_aprovacao_fisico_quimico" name="id_usuario_aprovacao_fisico_quimico">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina11" style="display: none;">
                        <h4 class="mt-4">7. Registro de Análises do Controle de Qualidade Microbiológico </h4>

                        <h6  class="mb-4 mt-5">7.1 Endotoxinas </h6>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Ensaio </th>
                                    <th class="table-light text-center text-dark" scope="col"> Especificação </th>
                                    <th class="table-light text-center text-dark" scope="col"> Código do Equipamento </th>
                                    <th class="table-light text-center text-dark" scope="col"> Resultado </th>
                                    <th class="table-light text-center text-dark" scope="col"> Data </th>
                                    <th class="table-light text-center text-dark" scope="col"> Analista </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th class="table-light" rowspan="4"> <sup>a</sup>Endotoxinas: </th>
                                    <td> < 25 EU/ml  </td>
                                    <td rowspan="4">
                                        <select class="form-select" id="endotoxinas_codigo" name="endotoxinas_codigo">
                                            <option selected disabled>Selecione o código</option>
                                            <option value="0">CQMB-END-001</option>
                                            <option value="1">CQMB-END-002</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="endotoxinas_1_resultado" name="endotoxinas_1_resultado">
                                    </td>
                                    <td rowspan="4">
                                        <input type="date" class="form-control" id="endotoxinas_data" name="endotoxinas_data">
                                    </td>
                                    <td rowspan="4" scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_endotoxinas" name="id_usuario_endotoxinas">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td> Spike recovery: 50-200% </td>

                                    <td>
                                        <input type="text" class="form-control" id="endotoxinas_2_resultado" name="endotoxinas_2_resultado">
                                    </td>
                                </tr>
                                <tr>
                                    <td> Sample Rxn Time CV < 25% </td>

                                    <td>
                                        <input type="text" class="form-control" id="endotoxinas_3_resultado" name="endotoxinas_3_resultado">
                                    </td>
                                </tr>
                                <tr>
                                    <td> Spike Rxn Time CV < 25% </td>

                                    <td>
                                        <input type="text" class="form-control" id="endotoxinas_4_resultado" name="endotoxinas_4_resultado">
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="table-light"> Código de Calibração (PTS): </th>
                                    <td>
                                        <input type="text" class="form-control" id="codigo_calibracao_pts" name="codigo_calibracao_pts">
                                    </td>
                                    <th colspan="2" class="table-light"> Lote do cartucho (PTS): </th>
                                    <td>
                                        <input type="text" class="form-control" id="lote_cartucho_pts" name="lote_cartucho_pts">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <label class="text-sm"> 
                            <sup>a</sup> British Pharmacopeia. Vol. III, 2009.
                        </label>

                        <h6  class="mb-4 mt-5">7.1 Teste de Integridade de Membrana </h6>

                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Equipamento </th>
                                    <th class="table-light text-center text-dark" scope="col"> Lote da Membrana </th>
                                    <th class="table-light text-center text-dark" scope="col"> Prazo de validade Membrana </th>
                                    <th class="table-light text-center text-dark" scope="col"> Fornecedor </th>
                                    <th class="table-light text-center text-dark" scope="col"> Analista </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" id="membrana_equipamento" name="membrana_equipamento">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="membrana_lote" name="membrana_lote">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control" id="membrana_validade" name="membrana_validade">
                                    </td>
                                    <td>MILLIPORE (Millex GS)</td>
                                    <td class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_membrana" name="id_usuario_membrana">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Pressão do teste de bolha fornecida pelo fabricante </th>
                                    <th class="table-light text-center text-dark" scope="col"> Pressão obtida no teste </th>
                                    <th class="table-light text-center text-dark" scope="col"> Analista </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>
                                        <input type="text" class="form-control" id="pressao_teste_bolha_fornecida" name="pressao_teste_bolha_fornecida">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="pressao_teste_bolha_obtida" name="pressao_teste_bolha_obtida">
                                    </td>
                                    <td class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_pressao_teste_bolha" name="id_usuario_pressao_teste_bolha">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <label class="text-sm"> 
                            Obs: <br>
                            1. A pressão do teste deve ser superior ou igual à pressão especificada pelo fabricante. <br>
                            2. Pelo menos 1 filtro deve estar dentro das especificações estabelecidas para aprovação do lote. 
                        </label>
                    
                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p13" name="ocorrencias_p13" maxlength="220"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p13" name="id_usuario_verificacao_ocorrencias_p13">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Aprovado? </th>
                                    <td>
                                        <select class="form-select" id="aprovacao_microbiologico" name="aprovacao_microbiologico">
                                            <option selected hidden></option>
                                            <option value="0"> Nâo </option>
                                            <option value="1"> Sim </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Data: </th>
                                    <td>
                                        <input type="date" class="form-control" id="data_aprovacao_microbiologico" name="data_aprovacao_microbiologico">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Farmacêutico Responsável: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_aprovacao_microbiologico" name="id_usuario_aprovacao_microbiologico">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina12" style="display: none;">
                        <h6  class="mb-4 mt-5">7.3 Esterilidade </h6>

                        <table class="table table-bordered">
                            <tbody class="text-center">
                                <tr>
                                    <th class="table-light"> Data do início da análise: </th>
                                    <td>
                                        <input type="date" class="form-control" id="esterilidade_data_inicio_analise" name="esterilidade_data_inicio_analise">
                                    </td>
                                    <th class="table-light"> Analista: </th>
                                    <td scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_esterilidade" name="id_usuario_esterilidade">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                                <tr class="">
                                    <th class="table-light text-center text-dark" scope="col"> Ensaio </th>
                                    <th class="table-light text-center text-dark" scope="col"> Especificação </th>
                                    <th class="table-light text-center text-dark" scope="col"> Código do Equipamento </th>
                                    <th class="table-light text-center text-dark" scope="col"> Resultado </th>
                                    <th class="table-light text-center text-dark" scope="col"> Data </th>
                                    <th class="table-light text-center text-dark" scope="col"> Analista </th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th class="table-light" rowspan="3"> Esterilidade: </th>
                                    <td> 1º Controle: Ausência de turvação e/ou depósito. </td>
                                    <td rowspan="3">
                                        <select class="form-select" id="esterilidade_codigo" name="esterilidade_codigo">
                                            <option selected disabled>Selecione o código</option>
                                            <option value="1"> CQFQ-EST-001</option>
                                            <option value="2"> CQFQ-EST-002</option>
                                            <option value="3"> CQFQ-EST-003</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="esterilidade_1_resultado" name="esterilidade_1_resultado">
                                    </td>
                                    <td >
                                        <input type="date" class="form-control" id="esterilidade_1_data" name="esterilidade_1_data">
                                    </td>
                                    <td scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_esterilidade_1" name="id_usuario_esterilidade_1">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td> 2º Controle: Ausência de turvação e/ou depósito. </td>
                                    <td>
                                        <input type="text" class="form-control" id="esterilidade_2_resultado" name="esterilidade_2_resultado">
                                    </td>
                                    <td >
                                        <input type="date" class="form-control" id="esterilidade_2_data" name="esterilidade_2_data">
                                    </td>
                                    <td scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_esterilidade_2" name="id_usuario_esterilidade_2">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td> 3º Controle: Ausência de turvação e/ou depósito. </td>
                                    <td>
                                        <input type="text" class="form-control" id="esterilidade_3_resultado" name="esterilidade_3_resultado">
                                    </td>
                                    <td >
                                        <input type="date" class="form-control" id="esterilidade_3_data" name="esterilidade_3_data">
                                    </td>
                                    <td scope="col">
                                        <div class="flex align-items-center">
                                            <select class="form-select ms-2" id="id_usuario_esterilidade_3" name="id_usuario_esterilidade_3">
                                                <option selected disabled>Selecione um usuário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Ocorrências: </th>
                                    <td>
                                        <textarea class="form-control" id="ocorrencias_p14" name="ocorrencias_p14" maxlength="260"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Verificado Por: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_verificacao_ocorrencias_p14" name="id_usuario_verificacao_ocorrencias_p14">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <table class="table table-bordered mt-5">
                            <tbody class="text-center">
                                <tr>
                                    <th> Aprovado? </th>
                                    <td>
                                        <select class="form-select" id="aprovacao_esterilidade" name="aprovacao_esterilidade">
                                            <option selected hidden></option>
                                            <option value="0"> Nâo </option>
                                            <option value="1"> Sim </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Data: </th>
                                    <td>
                                        <input type="date" class="form-control" id="data_aprovacao_esterilidade" name="data_aprovacao_esterilidade">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="text-center">
                                <tr>
                                    <th class="table-light" scope="col"> Farmacêutico Responsável: </th>
                                    <td colspan="5" class="table-light" scope="col">
                                        <select class="form-select" id="id_usuario_aprovacao_esterilidade" name="id_usuario_aprovacao_esterilidade">
                                            <option selected disabled>Selecione um usuário</option>
                                            @foreach($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="pagina13" style="display: none;">
                        <h4 class="mt-4 mb-5">8. Registro de Aprovação/Reprovação de Lote de <sup>18</sup>FDG </h4>

                        <div class="d-flex align-items-center">
                            <h6 class="me-2"> Supervisor de Controle de Qualidade: </h6>
                            <select class="form-select w-auto" id="id_usuario_supervisor_controle_qualidade" name="id_usuario_supervisor_controle_qualidade">
                                <option selected disabled>Selecione um usuário</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center mt-4">
                            <h6 class="me-2"> O produto acabado atende aos critérios de aceitação estabelecidos nas farmacopéias oficiais nacionalmente aceitas? </h6>                            
                            <select class="form-select w-auto" id="atendimento_criterios" name="atendimento_criterios">
                                <option selected disabled></option>
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center mt-4">
                            <h6 class="me-2">O Lote está</h6>
                            <select class="form-select w-auto" id="aprovacao_lote" name="aprovacao_lote">
                                <option selected disabled></option>
                                <option value="1">Aprovado</option>
                                <option value="0">Reprovado</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center">
                            <h6 class="me-2"> Responsável pela Garantia da Qualidade: </h6>
                            <select class="form-select w-auto" id="id_usuario_resposavel_garantia_qualidade" name="id_usuario_resposavel_garantia_qualidade">
                                <option selected disabled>Selecione um usuário</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center mt-4">
                            <h6 class="me-2">Hora da emissão do laudo:</h6>
                            <input type="time" class="form-control w-auto" id="hora_emissao_laudo" name="hora_emissao_laudo">

                        </div>
                    </div>


                    <!-- Botões de Navegação -->
                    <div class="text-center mt-5">
                        <button type="button" id="btn-voltar" class="btn btn-secondary px-5" onclick="voltarPagina()">Voltar</button>
                        <button type="button" id="btn-proximo" class="btn btn-orange px-5" onclick="proximaPagina()">Próximo</button>
                        <button type="submit" id="btn-salvar" class="btn btn-orange px-5">Salvar Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let paginaAtual = 1;
        let paginaAnterior = 1;
        const totalPaginas = 13;

        function atualizarVisibilidade() {
            document.getElementById('pagina'+paginaAnterior).style.display = 'none';
            document.getElementById('pagina'+paginaAtual).style.display = 'block';
            
            document.getElementById('btn-voltar').style.display = (paginaAtual > 1) ? 'inline-block' : 'none';
            document.getElementById('btn-proximo').style.display = (paginaAtual < totalPaginas) ? 'inline-block' : 'none';
            document.getElementById('btn-salvar').style.display = (paginaAtual === totalPaginas) ? 'inline-block' : 'none';
        }

        function proximaPagina() {
            if (paginaAtual < totalPaginas) {
                paginaAnterior = paginaAtual;
                paginaAtual++;
                atualizarVisibilidade();
            }
        }

        function voltarPagina() {
            if (paginaAtual > 1) {
                paginaAnterior = paginaAtual;
                paginaAtual--;
                atualizarVisibilidade();
            }
        }
        atualizarVisibilidade();

    </script>
</x-app-layout>
