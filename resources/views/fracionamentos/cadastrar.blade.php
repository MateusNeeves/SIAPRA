<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{$qtd.'º Fracionamento de '.today()->format('d/m/Y')}}
                </div>
                <form class="flex" method="POST" action="{{route('fracionamentos.store')}}">
                    @csrf
                    <div>
                        <div class="container">
                            <table class="table table-bordered">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th scope="col"> Ativ. EOB Calculada (mCi) </th>
                                        <th scope="col"> Ativ. EOB Real (mCi) </th>
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
                                            <td style="background-color: rgb(229 231 235);"><input class="bg-transparent border-0 text-center w-100" type="number" name="ativ_dest[{{$idx}}]" id="ativ_dest[{{$idx}}]" value="{{round(old('ativ_dest')[$idx] ?? $pedido_plan->ativ_dest)}}" required></td>
                                            <td> {{session()->get('ativ_eos')[$idx] ?? ''}}</td>  
                                            <td> {{session()->get('vol_frasco')[$idx] ?? ''}}</td>  
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <div class="flex justify-content-center">
                                <button name="action" value="calculate" class="mb-4">
                                    <a class="btn btn-dark" >
                                        {{'Calcular'}}
                                    </a>
                                </button>
                            </div>
    
                            @if (session()->has('ativ_eos'))
                                <div class="flex justify-content-center">
                                    <button name="action" value="save" class="mb-4">
                                        <a class="btn btn-dark" >
                                            {{ __('Salvar Fracionamento') }}
                                        </a>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <table class="table table-bordered ms-4 h6" style="width: fit-content; height: fit-content;">
                        <thead class="table-dark text-center">
                            <tr>
                                <th scope="col"> Qtd. Doses </th>
                                <th scope="col"> Atividade </th>
                            </tr>
                        </thead>
                        
                        <tbody class="text-center">
                            @for ($i = 0, $ativ_num_dose = 0 ; $i < 10 ; $i++)
                            <tr>
                                <td> {{$i + 1}} </td>
                                <td> {{round($ativ_num_dose = $planejamento->ativ_dose + ($ativ_num_dose * exp(M_LN2 * $planejamento->tempo_exames / 109.7))) .  ' mCi'}}</td>
                            </tr>  
                            @endfor
                        </tbody>
                    </table>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>