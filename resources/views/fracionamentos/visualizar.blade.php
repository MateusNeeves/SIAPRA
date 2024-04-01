<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Fracionamentos'}}
                </div>

                <form method="POST" action="{{route('fracionamentos.show')}}">
                    @csrf
                    <div class="flex justify-content-center">
                        <input class="me-1 bg-secondary border rounded border-dark" id="data_producao" type="date" name="data_producao" value="{{old('data_producao')}}" required>
                        <button>
                            <a class="btn btn-dark" >
                                {{ __('Visualizar Fracionamento') }}
                            </a>
                        </button>
                    </div>

                    <div class="flex justify-content-center">
                        <a class="btn btn-dark my-5" href="{{route('fracionamentos.register')}}">
                            {{ __('Novo Fracionamento') }}
                        </a>
                    </div>
                </form>

                @if (session()->has('fracionamento'))
                    @php
                        $fracionamento = session()->get('fracionamento');
                        $pedidos_frac = session()->get('pedidos_frac');       
                    @endphp

                    <div class="flex justify-content-end mb-2 me-5">
                        <button id="btnPdf" class="btn btn-dark bg-gradient" name="{{'Fracionamento_' . $fracionamento->data_producao . '.pdf'}}"> PDF </button>
                    </div>
                    <div id="divPdf" class="bg-white p-4">
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
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr>
                                        <td>{{$fracionamento->ativ_eob_calc}}</td>
                                        <td>{{$fracionamento->ativ_eob_real}}</td>
                                        <td>{{$fracionamento->fim_sintese}}</td>
                                        <td>{{$fracionamento->ativ_eos_nec}}</td>
                                        <td>{{$fracionamento->ativ_eos_real}}</td>
                                        <td>{{$fracionamento->vol_eos}}</td>
                                        <td>{{$fracionamento->ativ_esp}}</td>
                                    </tr>  
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div class="container">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th scope="col"> # </th>
                                        <th scope="col"> Cliente </th>
                                        <th scope="col"> Qtd Doses </th>
                                        <th scope="col"> Tempo de Transporte (min) </th>
                                        <th scope="col"> Volume no Frasco (ml) </th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($pedidos_frac as $pedido_frac)
                                        <tr>
                                            <td> {{$pedido_frac->id}}</td>  
                                            <td> {{$pedido_frac->nome_fantasia}}</td>  
                                            <td> {{$pedido_frac->qtd_doses}}</td>  
                                            <td> {{$pedido_frac->tempo_transp}}</td>  
                                            <td> {{$pedido_frac->vol_real_frasco}}</td>  
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