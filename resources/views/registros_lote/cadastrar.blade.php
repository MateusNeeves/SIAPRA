<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{ 'Novo Registro de Lote' }}
                </div>

                <form action="{{ route('registros_lote.store') }}" method="POST">
                    @csrf

                    <!-- Lote e Data de Fabricação -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="lote" class="form-label">Lote</label>
                            <input type="text" class="form-control" id="lote" name="lote" required>
                        </div>
                        <div class="col-md-6">
                            <label for="data_fabricacao" class="form-label">Data de Fabricação</label>
                            <input type="date" class="form-control" id="data_fabricacao" name="data_fabricacao" required>
                        </div>
                    </div>

                    <!-- Página 3 - Irradiação -->
                    <h4 class="mt-4">Irradiação</h4>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="lote_agua_enriquecida" class="form-label">Lote da Água Enriquecida</label>
                            <input type="text" class="form-control" id="lote_agua_enriquecida" name="lote_agua_enriquecida" required>
                        </div>
                    </div>

                    <!-- Pressões e Radiação -->
                    <h4 class="mt-4">Medições</h4>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="pressao_ar_comprimido" class="form-label">Pressão de Ar Comprimido (bar)</label>
                            <input type="number" step="0.1" class="form-control" id="pressao_ar_comprimido" name="pressao_ar_comprimido" required>
                        </div>
                        <div class="col-md-3">
                            <label for="pressao_H" class="form-label">Pressão de H 6.0 (bar)</label>
                            <input type="number" step="0.1" class="form-control" id="pressao_H" name="pressao_H" required>
                        </div>
                        <div class="col-md-3">
                            <label for="pressao_He_refrigeracao" class="form-label">Pressão de He de Refrigeração 4.5 (bar)</label>
                            <input type="number" step="0.1" class="form-control" id="pressao_He_refrigeracao" name="pressao_He_refrigeracao" required>
                        </div>
                        <div class="col-md-3">
                            <label for="pressao_He_analitico" class="form-label">Pressão de He 5.0 Analítico (bar)</label>
                            <input type="number" step="0.1" class="form-control" id="pressao_He_analitico" name="pressao_He_analitico" required>
                        </div>
                        <div class="col-md-3">
                            <label for="radiacao_ambiental_lab" class="form-label">Radiação Ambiental no Laboratório de Produção (µSv/h)</label>
                            <input type="number" step="0.1" class="form-control" id="radiacao_ambiental_lab" name="radiacao_ambiental_lab" required>
                        </div>
                    </div>

                    <!-- Horários -->
                    <h4 class="mt-4">3.1.1 Realizar irradiação da água enriquecida em O18 95% </h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hora_inicio_irradiacao_agua_enriquecida" class="form-label">Início (h)</label>
                            <input type="time" class="form-control" id="hora_inicio_irradiacao_agua_enriquecida" name="hora_inicio_irradiacao_agua_enriquecida" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hora_final_irradiacao_agua_enriquecida" class="form-label">Fim (h)</label>
                            <input type="time" class="form-control" id="hora_final_irradiacao_agua_enriquecida" name="hora_final_irradiacao_agua_enriquecida" required>
                        </div>
                        <div class="col-md-6">
                            <label for="ativ_teorica_F18" class="form-label">Atividade Teórica do F18 (mCi)</label>
                            <input type="number" class="form-control" id="ativ_teorica_F18" name="ativ_teorica_F18" required>
                        </div>
                    </div>

                    <h4 class="mt-4">3.1.2 Transferir o F18 para o Módulo de Síntese  </h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hora_inicio_transferir_F18_sintese" class="form-label">Início (h)</label>
                            <input type="time" class="form-control" id="hora_inicio_transferir_F18_sintese" name="hora_inicio_transferir_F18_sintese" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hora_final_transferir_F18_sintese" class="form-label">Fim (h)</label>
                            <input type="time" class="form-control" id="hora_final_transferir_F18_sintese" name="hora_final_transferir_F18_sintese" required>
                        </div>
                    </div>

                    <!-- Ocorrências -->
                    <h4 class="mt-4">Ocorrências</h4>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ocorrencias_p3" class="form-label">Descrição</label>
                            <textarea class="form-control" id="ocorrencias_p3" name="ocorrencias_p3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="ocorrencias_horario_p3" class="form-label">Horário da Ocorrência</label>
                            <input type="time" class="form-control" id="ocorrencias_horario_p3" name="ocorrencias_horario_p3">
                        </div>
                    </div>

                    <!-- Logbook -->
                    <h4 class="mt-4">Logbook</h4>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Anexado?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="logbook_anexado" name="logbook_anexado">
                                <label class="form-check-label" for="logbook_anexado">Sim</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="logbook_data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="logbook_data" name="logbook_data">
                        </div>
                        <div class="col-md-4">
                            <label for="logbook_time" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="logbook_time" name="logbook_time">
                        </div>
                    </div>

                    <!-- Botão de Envio -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">Salvar Registro</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>