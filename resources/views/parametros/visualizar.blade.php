<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Parâmetros'}}
                </div>

                {{-- <a class="btn btn-dark mb-4" href={{$path}} >
                    {{"Cadastrar Novo"}}
                </a> --}}
                <div class="container">
                    <table id="paramTable" class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th class="table-dark text-start" scope="col"> {{$column}} </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($indexes as $index)
                                    <td class="text-start"> {{$parametros[$index]}}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mb-4"></div>
            </div>
        </div>
    </div>
</x-app-layout>

    <!-- Modal EDITAR-->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Editando Parâmetros</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('parametros.update')}}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Atividade Por Dose -->
                        <div>
                            <x-input-label :value="__('Atividade Por Dose *')" />
                            <x-text-input id="ativ_dose" class="block mt-1 w-full" type="number" name="ativ_dose" :value="old('ativ_dose')" required autofocus/>
                        </div>

                        <!-- Tempo Entre Exames -->
                        <div class="mt-4">
                            <x-input-label :value="__('Tempo Entre Exames *')" />
                            <x-text-input id="tempo_exames" class="block mt-1 w-full" type="number" name="tempo_exames" :value="old('tempo_exames')" required/>
                        </div>

                        <!-- Volume p/ C.Q. -->
                        <div class="mt-4">
                            <x-input-label :value="__('Volume p/ C.Q. *')" />
                            <x-text-input id="vol_max_cq" class="block mt-1 w-full" type="number" name="vol_max_cq" :value="old('vol_max_cq')" required/>
                        </div>

                        <!-- Tempo de Expedição -->
                        <div class="mt-4">
                            <x-input-label :value="__('Tempo de Expedição *')" />
                            <x-text-input id="tempo_exped" class="block mt-1 w-full" type="number" name="tempo_exped" :value="old('tempo_exped')" required/>
                        </div>

                        <!-- Rend. Típico do Cíclotron -->
                        <div class="mt-4">
                            <x-input-label :value="__('Rend. Típico do Cíclotron *')" />
                            <x-text-input id="rend_tip_ciclotron" class="block mt-1 w-full" type="number" name="rend_tip_ciclotron" :value="old('rend_tip_ciclotron')" required/>
                        </div>

                        <!-- Corrente Alvo -->
                        <div class="mt-4">
                            <x-input-label :value="__('Corrente Alvo *')" />
                            <x-text-input id="corrente_alvo" class="block mt-1 w-full" type="number" name="corrente_alvo" :value="old('corrente_alvo')" required/>
                        </div>

                        <!-- Rend. da Síntese -->
                        <div class="mt-4">
                            <x-input-label :value="__('Rend. da Síntese *')" />
                            <x-text-input id="rend_sintese" class="block mt-1 w-full" type="number" name="rend_sintese" :value="old('rend_sintese')" required/>
                        </div>

                        <!-- Tempo da Síntese -->
                        <div class="mt-4">
                            <x-input-label :value="__('Tempo da Síntese *')" />
                            <x-text-input id="tempo_sintese" class="block mt-1 w-full" type="number" name="tempo_sintese" :value="old('tempo_sintese')" required/>
                        </div>

                        <!-- Volume EOS -->
                        <div class="mt-4">
                            <x-input-label :value="__('Volume EOS *')" />
                            <x-text-input id="vol_eos" class="block mt-1 w-full" type="number" name="vol_eos" :value="old('vol_eos')" required/>
                        </div>

                        <!-- Horário da Saída -->
                        <div class="mt-4">
                            <x-input-label :value="__('Horário da Saída *')" />
                            <x-text-input id="hora_saida" class="block mt-1 w-full" type="time" name="hora_saida" :value="old('hora_saida')" required/>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
