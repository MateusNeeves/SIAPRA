<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$qtd.'º Fracionamento de '.today()->format('d/m/Y')}}
                </div>
                <form method="POST" action="{{route('fracionamentos.store')}}">
                    @csrf

                    <div class="container">
                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th scope="col"> Ativ. EOB Calculada (mCi) </th>
                                    <th scope="col"> Ativ. EOB Real (mCi) </th>
                                    <th scope="col"> Fim da Síntese </th>
                                    <th scope="col"> Ativ. EOS Necessária (MBq) </th>
                                    <th scope="col"> Ativ. EOS Real (MBq) </th>
                                    <th scope="col"> Vol. EOS (mL)</th>
                                    <th scope="col"> Ativ. Específica (mCi/mL)</th>
                                    <th scope="col"> Rend. Síntese Real (%)</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>{{$planejamento->ativ_eob}}</td>
                                    <td style="background-color: rgb(229 231 235);"><input class="bg-transparent border-0 text-center w-100" id="ativ_eob_real" type="number" name="ativ_eob_real" value="{{old('ativ_eob_real')}}" required></td>
                                    <td>
                                        <button name="action" value="calculate">
                                            <a class="btn bg-danger border border-dark" >
                                                {{ session()->get('fim_sintese') ?? 'Aperte' }}
                                            </a>
                                        </button>
                                    </td>
                                    <td>{{session()->get('ativ_eos_nec') ?? ''}}</td>
                                    <td style="background-color: rgb(229 231 235);"><input class="bg-transparent border-0 text-center w-100" id="ativ_eos_real" type="number" name="ativ_eos_real" value="{{old('ativ_eos_real')}}" required></td>
                                    <td style="background-color: rgb(229 231 235);"><input class="bg-transparent border-0 text-center w-100" id="vol_eos" type="number" name="vol_eos" value="{{old('vol_eos')}}" required></td>
                                    <td>{{session()->get('ativ_esp') ?? ''}}</td>
                                    <td>{{session()->get('rend_sintese_real') ?? ''}}</td>

                                </tr>  
                            </tbody>
                        </table>
                    </div>

                    <div class="container pt-5">
                        <table class="table table-bordered">
                            <thead class="table-dark text-center">
                                <tr> 
                                    <th class="header" scope="col"> # </th>
                                    <th class="header" scope="col"> Cliente </th>
                                    <th class="header" scope="col"> Qtd Doses </th>
                                    <th class="header" scope="col"> Tempo de Transporte (min) </th>
                                    <th class="header" scope="col"> Atividade no Destino (mCi) </th>
                                    <th class="header" scope="col"> Atividade EOS (mCi) </th>
                                    <th class="header" scope="col"> Volume no Frasco (ml) </th>                   
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td colspan="1"></td>
                                    <td class="item"> Controle de Qualidade </td>
                                    <td colspan="1"></td>
                                    <td colspan="1"></td>
                                    <td colspan="1"></td>
                                    <td class="item"> {{session()->get('ativ_cq') ?? ''}} </td>
                                    <td class="item"> {{$planejamento->vol_max_cq}} </td>
                                </tr>
                                @foreach ($pedidos_plan as $idx => $pedido_plan)
                                    <tr>
                                        <td> {{$pedido_plan->id_pedido}}</td>  
                                        <td> {{$pedido_plan->nome_fantasia}}</td>  
                                        <td> {{$pedido_plan->qtd_doses_selec}}</td>  
                                        <td> {{$pedido_plan->tempo_transp}}</td>  
                                        <td> {{$pedido_plan->ativ_dest}}</td>  
                                        <td> {{session()->get('ativ_eos')[$idx] ?? ''}}</td>  
                                        <td> {{session()->get('vol_frasco')[$idx] ?? ''}}</td>  
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <div class="flex justify-content-center">
                            <button name="action" value="save" class="mb-4">
                                <a class="btn btn-dark" >
                                    {{ __('Salvar Fracionamento') }}
                                </a>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>