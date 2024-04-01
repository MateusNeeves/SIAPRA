<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Planejamento'}}
                </div>

                <form method="POST" action="{{route('planejamentos.show')}}">
                    @csrf
                    <div class="flex justify-content-center">
                        <input class="me-1 bg-secondary border rounded border-dark" id="data_producao" type="date" name="data_producao" value="{{old('data_producao')}}" required>
                        <button>
                            <a class="btn btn-dark" >
                                {{ __('Visualizar Planejamento') }}
                            </a>
                        </button>
                    </div>

                    <div class="flex justify-content-center">
                        <a class="btn btn-dark my-5" href="{{route('planejamentos.register')}}">
                            {{ __('Novo Planejamento') }}
                        </a>
                    </div>
                </form>

                @if (session()->has('planejamento'))
                    @php
                        $planejamento = session()->get('planejamento');
                        $pedidos_plan = session()->get('pedidos_plan');       
                    @endphp
                    
                    <div class="flex justify-content-end mb-2 me-5">
                        <button id="btnPdf" class="btn btn-dark bg-gradient" name="{{'Planejamento_' . $planejamento->data_producao . '.pdf'}}"> PDF </button>
                    </div>

                    <div id="divPdf" class="bg-white p-4">
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
                                        <td class="text-end">{{$planejamento->data_producao}}</td>
                                        <td colspan="1"></td>
                
                                        <!-- Cíclotron -->
                                        <td class="bg-secondary text-white text-center"> Início </td>
                                        <td class="text-end">{{session()->get('inicio_ciclotron')}}</td>
                                        <td class="text-start">{{'h'}}</td>
                                        
                                        <!-- Síntese -->    
                                        <td class="bg-secondary text-white text-center"> Início </td>
                                        <td class="text-end">{{session()->get('inicio_sintese') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>
                                    </tr>
                
                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="bg-secondary text-white text-center"> Horário de Saída </td>
                                        <td class="text-end">{{$planejamento->hora_saida}}</td>
                                        <td class="text-start">{{"h"}}</td>

                                        <!-- Cíclotron -->    
                                        <td class="bg-secondary text-white text-center"> Fim </td>
                                        <td class="text-end">{{session()->get('fim_ciclotron') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>

                                        <!-- Síntese -->
                                        <td class="bg-secondary text-white text-center"> Fim </td>
                                        <td class="text-end">{{session()->get('fim_sintese') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>
                                    </tr>
                                
                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="bg-secondary text-white text-center"> Atividade por Dose </td>
                                        <td class="text-end">{{$planejamento->ativ_dose}}</td>
                                        <td class="text-start">{{"mCi"}}</td>

                                        <!-- Cíclotron -->    
                                        <td class="bg-secondary text-white text-center"> Duração </td>
                                        <td class="text-end">{{session()->get('dur_ciclotron')}}</td>
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
                                        <td class="bg-secondary text-white text-center"> Atividade EOB </td>
                                        <td class="text-end">{{$planejamento->ativ_eob}}</td>
                                        <td class="text-start">{{"mCi"}}</td>

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
                                        <td colspan="1"></td>
                                        <td colspan="1"></td>
                                        <td colspan="1"></td>
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
                                    @foreach ($pedidos_plan as $pedido_plan)
                                        <tr>
                                            <td> {{$pedido_plan->id}}</td>  
                                            <td> {{$pedido_plan->nome_fantasia}}</td>  
                                            <td> {{$pedido_plan->qtd_doses}}</td>  
                                            <td> {{$pedido_plan->tempo_transp}}</td>  
                                            <td> {{$pedido_plan->ativ_dest}}</td>  
                                            <td> {{$pedido_plan->vol_frasco}}</td>  
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>