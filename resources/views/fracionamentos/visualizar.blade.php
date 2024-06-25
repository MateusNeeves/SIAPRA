<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Fracionamentos'}}
                </div>

                <form method="POST" action="{{route('fracionamentos.show')}}">
                    @csrf
                    <div class="flex justify-content-center mb-5">
                        <a class="btn btn-dark" href="{{route('fracionamentos.register')}}">
                            {{ __('Novo Fracionamento') }}
                        </a>
                    </div>
                    <div class="flex justify-content-center mb-5">
                        <div class="position-relative" style="width: 230px">
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
                            <button disabled class="btn btn-dark ms-2" id="visualizar_button" style="width: 230px">
                                {{ __('Visualizar Fracionamento') }}
                            </button>
                        </button>
                    </div>
                </form>

                @if (session()->has('fracionamentos'))
                    @php
                        $fracionamentos = session()->get('fracionamentos');
                        $pedidos_frac = session()->get('pedidos_frac');       
                        $data_producao = session()->get('data_producao');       
                    @endphp
                    <div class="flex justify-content-end mb-2 me-5">
                        <button id="pdfBtn" onclick="printPdf('Fracionamento_'+'{{$data_producao}}'+'.pdf')" class="btn btn-dark bg-gradient"> PDF </button>
                    </div>
                    <div id="divPdf" class="bg-white mb-4">
                        @foreach ($fracionamentos as $idx => $fracionamento)
                            <div class="{{$idx == 0 ? '' : 'breakPage'}}">
                                <div class="flex">
                                    <button class="btn btn-dark mb-2" style="display: inline-flex !important; align-items: center !important;" data-bs-toggle="collapse" data-bs-target="#collapse_{{$idx}}">
                                        <div>{{$idx+1 . 'º Fracionamento '}}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </div>

                                <div class="collapse mb-5 bg-white" id="collapse_{{$idx}}">
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
                                                    <th scope="col"> Fim da Síntese </th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                <tr>
                                                    <td>{{$fracionamento->ativ_eob}}</td>
                                                    <td>{{$fracionamento->ativ_eob_real}}</td>
                                                    <td>{{$fracionamento->ativ_eos_nec}}</td>
                                                    <td>{{$fracionamento->ativ_eos_real}}</td>
                                                    <td>{{$fracionamento->vol_eos}}</td>
                                                    <td>{{$fracionamento->ativ_esp}}</td>
                                                    <td>{{Str::substr($fracionamento->fim_sintese, 0, 5)}}</td>
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
                                                    <th scope="col"> Qtd Doses Entregues </th>
                                                    <th scope="col"> Atividade no Destino (mCi) </th>
                                                    <th scope="col"> Tempo de Transporte (min) </th>
                                                    <th scope="col"> Volume no Frasco (ml) </th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                @foreach ($pedidos_frac[$idx] as $pedido_frac)
                                                    <tr>
                                                        <td> {{$pedido_frac->id}}</td>  
                                                        <td> {{$pedido_frac->nome_fantasia}}</td>  
                                                        <td> {{$pedido_frac->qtd_doses_selec}}</td>  
                                                        <td> {{$pedido_frac->qtd_doses_entregues}}</td>  
                                                        <td> {{$pedido_frac->ativ_dest}}</td>  
                                                        <td> {{$pedido_frac->tempo_transp}}</td>  
                                                        <td> {{$pedido_frac->vol_real_frasco}}</td>  
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
</x-app-layout>