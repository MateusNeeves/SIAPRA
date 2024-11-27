<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    Pesquisa dos Logs
                </div>

                <form method="get" action="{{route('logs.view')}}">
                    <!-- Nome do Usuário -->
                    <div class="mt-4">
                        <x-input-label :value="__('Nome do Usuário')" />
                        <x-text-input id="user" class="block mt-1 w-full" type="text" name="user" :value="old('user')"/>
                    </div>

                    <!-- Busca Geral -->
                    <div class="mt-4">
                        <x-input-label :value="__('Busca Geral')" />
                        <x-text-input id="generalSearch" class="block mt-1 w-full" type="text" name="generalSearch" :value="old('generalSearch')"/>
                    </div>

                    <!-- Data Inicial -->
                    <div class="mt-4">
                        <x-input-label :value="__('Data Inicial *')" />
                        <x-text-input id="startDate" class="block mt-1 w-full" type="date" name="startDate" :value="old('startDate')" required/>
                    </div>

                    <!-- Data Final -->
                    <div class="mt-4">
                        <x-input-label :value="__('Data Final *')" />
                        <x-text-input id="endDate" class="block mt-1 w-full" type="date" name="endDate" :value="old('endDate')" required/>
                    </div>

                    <!-- Ações -->
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <x-input-label :value="__('Ações *')" />
                            <x-input-label :value="__('(Selecione ao menos 1 opção)')" />
                        </div>
                        <select class="multiple-select block mt-1 w-full border rounded" name="acoes[]" multiple required>
                            @foreach ($acoes as $acao)
                                <option value="{{$acao}}" {{in_array($acao, old('acoes') ?? []) ? "selected" : ""}} > {{$acao}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="my-4 flex justify-content-end">
                        <button type="submit" class="btn btn-orange">Pesquisar</button>
                    </div>
                </form>


            </div>
        </div>
    </div>

    
    @if (Session::has('modal'))
        <script>
            $(window).on('load', function() {
                var modals = {!! json_encode(Session::get('modal')) !!}; // Converte o array de strings para JSON
                var z = 1051 - (modals.length - 1) * 10; // Calcula o z-index inicial baseado no número de modais
            
                // Itera sobre os modais na ordem normal
                modals.forEach(function(modalId) {
                    $(modalId).modal('show'); // Mostra cada modal
                    $(modalId).css('z-index', z); // Define o z-index do modal
                    z += 10; // Aumenta o z-index para o próximo modal
                });
            });
        </script>
    @endif

    <script>
        function closeModal() {
            var modals = {!! json_encode(Session::get('modal')) !!}; // Converte o array de strings para JSON
            for (var i = modals.length - 1; i >= 0; i--) {
                var modalId = modals[i];
                var currentZ = parseInt($(modalId).css('z-index')); // Obtém o z-index atual e converte para número
                
                $(modalId).css('z-index', currentZ + 10); // Incrementa o z-index em 10
            }
        }
    </script>

    <!-- Modal VISUALIZAR LOGS-->
    @if (Session::has('modal') && in_array("#viewLogsModal", Session::get('modal')))
        <div class="modal fade" id="viewLogsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg para ajustar largura -->
                <div class="modal-content">
                    <div class="modal-header d-block">
                        <div class="d-flex">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Resultados da Pesquisa de Logs</h1>
                            <div class="ms-auto">
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-body">
                        @php
                            $logs = Session::get('logs');
                            $i = 0;
                        @endphp
                        <div class="div-scroll" style="max-height: 600px">
                            <table id="myTableSelect" class="table table-bordered table-scroll w-100">
                                <thead>
                                    <tr class="text-sm">
                                        <th class="text-center table-orange" scope="col"> # </th>
                                        <th class="text-center table-orange" scope="col"> Operação </th>
                                        <th class="text-center table-orange" scope="col"> Data e Hora </th>
                                        <th class="text-center table-orange" scope="col"> Usuário </th>
                                        <th hidden> i </th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-center">              
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td class="text-center">{{$log['id_log']}}</td>
                                            <td class="text-center">{{$log['acao']}}</td>
                                            <td class="text-center">{{$log['data_hora']}}</td>      
                                            <td class="text-center">{{$log['user_username']}}</td>      
                                            <td class="idx" hidden>{{$i++}}</td>      
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button disabled id="details_button" onclick="viewDetails()" class="btn btn-orange bg-gradient me-2" > Detalhes </button>
                        
                        <script>
                            $(document).on('click', function() {
                                if ($('#myTableSelect .selected').length){
                                    $('#details_button').prop('disabled', false);
                                }
                                else{
                                    $('#details_button').prop('disabled', true);
                                }
                            });

                            function viewDetails() {
                                var logs = @json($logs);

                                
                                var idx = parseInt($('#myTableSelect .selected .idx').text());
                                
                                console.log(logs[idx].id_log);
                                $('#detailsLogModal .modal-body #id_log').text(logs[idx].id_log);
                                $('#detailsLogModal .modal-body #acao').text(logs[idx].acao);
                                $('#detailsLogModal .modal-body #data_hora').text(logs[idx].data_hora);
                                $('#detailsLogModal .modal-body #user_id').text('- ID: ' + logs[idx].user_id);
                                $('#detailsLogModal .modal-body #user_username').text('- Username: ' + logs[idx].user_username);
                                $('#detailsLogModal .modal-body #user_name').text('- Nome: ' + logs[idx].user_name);
                                $('#detailsLogModal .modal-body #descricao').html(logs[idx].descricao.replace(/\n/g, '<br>'));

                                $('#viewLogsModal').css('z-index', 1040);
                                $('#detailsLogModal').modal('show');
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>
    @endif

        <!-- Modal DETALHES LOG-->
        @if (Session::has('modal') && in_array("#viewLogsModal", Session::get('modal')))
        <div class="modal fade" id="detailsLogModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg para ajustar largura -->
                <div class="modal-content">
                    <div class="modal-header d-block">
                        <div class="d-flex">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Detalhes do Log</h1>
                            <div class="ms-auto">
                                <button onclick="$('#viewLogsModal').css('z-index', 1051)" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-body">
                         <!-- Identificador -->
                        <div>
                            <x-input-label class="h6" :value="__('Identificador')" />
                            <x-input-label id="id_log" class="mt-2 text-secondary"  />
                        </div>

                        <!-- Ação -->
                        <div class="mt-4">
                            <x-input-label class="h6" :value="__('Operação')" />
                            <x-input-label id="acao" class="mt-2 text-secondary" />
                        </div>

                        <!-- Data-Hora -->
                        <div class="mt-4">
                            <x-input-label class="h6" :value="__('Data-Hora')" />
                            <x-input-label id="data_hora" class="mt-2 text-secondary" />
                        </div>

                        <!-- Usuário -->
                        <div class="mt-4">  
                            <x-input-label class="h6" :value="__('Usuário')" />
                            <x-input-label id="user_id" class="mt-2 text-secondary" />
                            <x-input-label id="user_username" class="text-secondary" />
                            <x-input-label id="user_name" class="text-secondary" />
                        </div>

                        <!-- Descrição -->
                        <div class="mt-4">
                            <x-input-label class="h6" :value="__('Descrição')" />
                            <x-input-label id="descricao" class="mt-2 text-secondary" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>