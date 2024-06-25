<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Planejamentos'}}
                </div>

                <form method="POST" action="{{route('planejamentos.show')}}">
                    @csrf
                    <div class="flex justify-content-center mb-5">
                        <a class="btn btn-dark" href="{{route('planejamentos.register')}}">
                            {{ __('Novo Planejamento') }}
                        </a>
                    </div>
                    <div class="flex justify-content-center mb-5">
                        <div class="position-relative" style="width: 220px">
                            <input class="btn btn-secondary border rounded border-dark placeholder-visible pe-3"  type="text" id="datePicker" name="data_producao" value="{{old('data_producao')}}" placeholder="Selecionar Data" readonly required>
                            <i class="bi bi-calendar3-week input-icon"></i>
                        </div>
                        
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Obtém as datas do backend passadas pelo Blade
                                const datas = @json($datas);
                                var field = document.getElementById('datePicker');
                                var button = document.getElementById('visualizar_button');

                                const datePicker = new Pikaday({
                                    field: field,
                                    onSelect: function(date) {
                                        field.value = moment(date).format('DD/MM/YYYY');
                                    },
                                    disableDayFn: function(date) {
                                        const dateString = date.toISOString().split('T')[0];
                                        return !datas.includes(dateString);
                                    },
                                    i18n: {
                                        previousMonth: 'Mês anterior',
                                        nextMonth: 'Próximo mês',
                                        months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                                        weekdays: ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'],
                                        weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
                                    }
                                });
                                if (field.value) {
                                    datePicker.setDate(moment(field.value, 'DD/MM/YYYY').toDate());
                                };
                            });
                            
                            document.getElementById('datePicker').addEventListener('change', function() {
                                if (document.getElementById('datePicker').value) {
                                    document.getElementById('visualizar_button').disabled = false;
                                }
                            });

                                              
                        </script>

                        <button>
                            <button disabled class="btn btn-dark  ms-3" id="visualizar_button" style="width: 220px">
                                {{ __('Visualizar Planejamento') }}
                            </button>
                        </button>
                    </div>

                </form>

                @if (session()->has('planejamentos'))
                    @php
                        $planejamentos = session()->get('planejamentos');
                        $pedidos_plan = session()->get('pedidos_plan');
                        $dur_ciclotron = session()->get('dur_ciclotron');
                        $inicio_ciclotron = session()->get('inicio_ciclotron');
                        $fim_ciclotron = session()->get('fim_ciclotron');
                        $inicio_sintese = session()->get('inicio_sintese');
                        $fim_sintese = session()->get('fim_sintese');
                        $data_producao = session()->get('data_producao');
                    @endphp
                    <div class="flex justify-content-end mb-2 me-5">
                        <button id="pdfBtn" onclick="printPdf('Planejamento_'+'{{ $data_producao}}'+'.pdf')" class="btn btn-dark bg-gradient"> PDF </button>
                    </div>
                    <div id="divPdf" class="mb-4 bg-white">
                        @foreach ($planejamentos as $idx => $planejamento)
                        <div class="{{$idx == 0 ? '' : 'breakPage'}}">
                            <div class="flex">
                                <button class="btn btn-dark mb-2" style="display: inline-flex !important; align-items: center !important;" data-bs-toggle="collapse" data-bs-target="#collapse_{{$idx}}">
                                    <div>{{$idx+1 . 'º Planejamento '}}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                                <div data-html2canvas-ignore="true" class="collapse collapse-horizontal ms-2" id="collapse_{{$idx}}">
                                    <button type="submit" class="btn btn-danger mb-2 me-5" data-id={{$planejamento->id}} data-bs-toggle="modal" data-bs-target="#deleteModal_" onclick="$('#deleteModal_ .modal-body #id').val({{$planejamento->id}})">Deletar</button>
                                </div>
                            </div>
                            <div class="collapse mb-5 bg-white" id="collapse_{{$idx}}">
                                <div class="container">
                                    <table class="table table-bordered">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th colspan="3" scope="colgroup"> Parâmetros Gerais </th>
                                                <th colspan="3" scope="colgroup"> Cíclotron </th>
                                                <th colspan="3" scope="colgroup"> Síntese </th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-start">
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td class="bg-secondary text-white text-center"> Data da Produção </td>
                                                <td class="text-end">
                                                    {{$data_producao}}
                                                </td>
                                                <td colspan="1"></td>
                        
                                                <!-- Cíclotron -->
                                                <td class="bg-secondary text-white text-center"> Início </td>
                                                <td class="text-end">{{$inicio_ciclotron[$idx]}}</td>
                                                <td class="text-start">{{'h'}}</td>
                                                
                                                <!-- Síntese -->    
                                                <td class="bg-secondary text-white text-center"> Início </td>
                                                <td class="text-end">{{$inicio_sintese[$idx]}}</td> 
                                                <td class="text-start">{{'h'}}</td>
                                            </tr>
                        
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td class="bg-secondary text-white text-center"> Horário de Saída </td>
                                                <td class="text-end">{{$planejamento->hora_saida}}</td>
                                                <td class="text-start">{{"h"}}</td>
        
                                                <!-- Cíclotron -->    
                                                <td class="bg-secondary text-white text-center"> Fim </td>
                                                <td class="text-end">{{$fim_ciclotron[$idx]}}</td> 
                                                <td class="text-start">{{'h'}}</td>
        
                                                <!-- Síntese -->
                                                <td class="bg-secondary text-white text-center"> Fim </td>
                                                <td class="text-end">{{$fim_sintese[$idx]}}</td> 
                                                <td class="text-start">{{'h'}}</td>
                                            </tr>
                                        
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td class="bg-secondary text-white text-center"> Atividade por Dose </td>
                                                <td class="text-end">{{$planejamento->ativ_dose}}</td>
                                                <td class="text-start">{{"mCi"}}</td>
        
                                                <!-- Cíclotron -->    
                                                <td class="bg-secondary text-white text-center"> Duração </td>
                                                <td class="text-end">{{$dur_ciclotron[$idx]}}</td>
                                                <td class="text-start">{{"h"}}</td>
        
                                                <!-- Síntese -->   
                                                <td class="bg-secondary text-white text-center"> Duração </td>
                                                <td class="text-end">{{$planejamento->tempo_sintese}}</td>
                                                <td class="text-start">{{"min"}}</td>
                                            </tr>
                                        
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td class="bg-secondary text-white text-center"> Volume Frascos C.Q. </td>
                                                <td class="text-end">{{$planejamento->vol_max_cq}}</td>
                                                <td class="text-start">{{"ml"}}</td>
        
                                                <!-- Cíclotron -->    
                                                <td class="bg-secondary text-white text-center"> Rendimento Típico do Cíclotron </td>
                                                <td class="text-end">{{$planejamento->rend_tip_ciclotron}}</td>
                                                <td class="text-start">{{"mCi/µAsat"}}</td>
        
                                                <!-- Síntese -->
                                                <td class="bg-secondary text-white text-center"> Rend. da Síntese </td>
                                                <td class="text-end">{{$planejamento->rend_sintese}}</td>
                                                <td class="text-start">{{"%"}}</td>
                                            </tr>
        
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td class="bg-secondary text-white text-center"> Tempo Entre Exames </td>
                                                <td class="text-end">{{$planejamento->tempo_exames}}</td>
                                                <td class="text-start">{{"min"}}</td>
        
                                                <!-- Cíclotron -->
                                                <td class="bg-secondary text-white text-center"> Corrente Alvo </td>
                                                <td class="text-end">{{$planejamento->corrente_alvo}}</td>
                                                <td class="text-start">{{"µA"}}</td>
        
                                                <!-- Síntese -->
                                                <td class="bg-secondary text-white text-center"> Volume EOS </td>
                                                <td class="text-end">{{$planejamento->vol_eos}}</td>
                                                <td class="text-start">{{"ml"}}</td>
                                            </tr>
        
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td class="bg-secondary text-white text-center"> Tempo de Expedição </td>
                                                <td class="text-end">{{$planejamento->tempo_exped}}</td>
                                                <td class="text-start">{{"min"}}</td>
        
                                                <!-- Cíclotron -->
                                                <td class="bg-secondary text-white text-center"> Fator de Segurança </td>
                                                <td class="text-end">{{$planejamento->fator_seguranca}}</td>
                                                <td class="text-start">{{"%"}}</td>
        
                                                <!-- Síntese -->
                                                <td class="bg-secondary text-white text-center"> Ativ. Esp. </td>
                                                <td class="text-end">{{$planejamento->ativ_esp}}</td>
                                                <td class="text-start">{{"mCi/ml"}}</td>
                                            </tr>
        
                                            <tr>
                                                <!-- Parâmetros Gerais -->
                                                <td colspan="1"></td>
                                                <td colspan="1"></td>
                                                <td colspan="1"></td>
        
                                                <!-- Cíclotron -->
                                                <td class="bg-secondary text-white text-center"> Atividade EOB </td>
                                                <td class="text-end">{{$planejamento->ativ_eob}}</td>
                                                <td class="text-start">{{"mCi"}}</td>
        
                                                <!-- Síntese -->
                                                <td class="bg-secondary text-white text-center"> Atividade EOS </td>
                                                <td class="text-end">{{$planejamento->ativ_eos}}</td>
                                                <td class="text-start">{{"mCi"}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div class="container">
                                    <table class="table table-bordered">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th scope="col"> # </th>
                                                <th scope="col"> Cliente </th>
                                                <th scope="col"> Qtd Doses </th>
                                                <th scope="col"> Tempo de Transporte (min) </th>
                                                <th scope="col"> Atividade no Destino (mCi) </th>
                                                <th scope="col"> Volume no Frasco (ml) </th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($pedidos_plan[$idx] as $pedido_plan)
                                                <tr>
                                                    <td> {{$pedido_plan->id}}</td>  
                                                    <td> {{$pedido_plan->nome_fantasia}}</td>  
                                                    <td> {{$pedido_plan->qtd_doses_selec}}</td>  
                                                    <td> {{$pedido_plan->tempo_transp}}</td>  
                                                    <td> {{$pedido_plan->ativ_dest}}</td>  
                                                    <td> {{$pedido_plan->vol_frasco}}</td>  
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal DELETAR-->
    <div class="modal fade" id="deleteModal_" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar Deleção</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('planejamentos.destroy')}}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        {{'Tem Certeza Que Deseja Deletar Esse Planejamento?'}}
                        <input hidden name="id" id="id" value="{{old('id')}}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar </button>
                        <button type="submit" class="btn btn-danger">Deletar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>