<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Novo Planejamento'}}
                </div>
                <form method="POST" action="{{route('planejamentos.store')}}">
                    @csrf

                    <div class="flex justify-content-center mb-5">
                        <div class="flex">
                            <div class="position-relative">
                                <input class="btn-orange border rounded border-dark placeholder-visible pe-3"  type="text" id="datePicker" name="data_producao" value="{{old('data_producao', $parametros['data_producao'])}}"placeholder="Selecione a Data da Produção" readonly required style="width: 300px">
                                <i class="bi bi-calendar3-week input-icon"></i>
                            </div>
                            <button disabled class="btn btn-orange ms-3" name="action" value="show" id="visualizar_button" style="width: 120px">
                                    {{ __('Confirmar') }}
                            </button>

                            <script>    
                                document.addEventListener("DOMContentLoaded", function () {
                                var field = document.getElementById('datePicker');
                                var button = document.getElementById('visualizar_button');

                                const datePicker = new Pikaday({
                                    field: field,
                                    onSelect: function (date) {
                                        field.value = moment(date).format('DD/MM/YYYY');
                                        button.disabled = false; // Habilita o botão quando uma data é selecionada
                                    },
                                    i18n: {
                                        previousMonth: 'Mês anterior',
                                        nextMonth: 'Próximo mês',
                                        months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                                        weekdays: ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'],
                                        weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
                                    }
                                });

                                // Se já houver uma data, carrega no picker e habilita o botão
                                if (field.value.trim() !== '') {
                                    datePicker.setDate(moment(field.value, 'DD/MM/YYYY').toDate());
                                    button.disabled = false;
                                } else {
                                    button.disabled = true;
                                }

                                // Adiciona um evento para monitorar mudanças no campo manualmente
                                field.addEventListener('change', function () {
                                    button.disabled = field.value.trim() === '';
                                });
                            });

                            </script>
                        </div>
                    </div>

                    @if (session()->has('pedidos'))
                        <br>
                        <div class="container">
                            <table class="table table-bordered">
                                <thead class="table-orange text-center">
                                    <tr colspan="8">
                                        <th colspan="8" scope="colgroup"> Pedidos </th>
                                    </tr>
                                    <tr>
                                        <th scope="col"> # </th>
                                        <th scope="col"> Cliente </th>
                                        <th scope="col"> Qtd Doses </th>
                                        <th scope="col"> Tempo de Transporte (min) </th>
                                        <th scope="col"> Atividade no Destino (mCi) </th>
                                        <th scope="col"> Atividade ao Sair (mCi) </th>
                                        <th scope="col"> Atividade EOS (mCi) </th>
                                        <th scope="col"> Volume no Frasco (ml) </th>
                                    </tr>
                                </thead>
                                <tbody >
                                    @foreach (session()->get('pedidos') as $idx => $pedido)
                                        <tr class="text-center" style="vertical-align: middle;">
                                            <td>{{$pedido->id}}</td>
                                            <td>{{$pedido->nome_fantasia}}</td>
                                            <td style="background-color: #eab1a0;">
                                                <select id="qtd_doses_selec" class="block mt-1 w-full bg-transparent border-0" name="qtd_doses_selec[{{$idx}}]" value="{{old('qtd_doses_selec')[$idx] ?? $pedido->qtd_doses}}" required>
                                                    @for ($i = 0; $i <= $pedido->qtd_doses ; $i++)
                                                        <option value="{{$i}}" {{old('qtd_doses_selec') ? (old('qtd_doses_selec')[$idx] == $i ? "selected" : "") : ($pedido->qtd_doses == $i ? "selected" : "")}}
                                                        > {{$i}} </option>
                                                    @endfor
                                                </select>
                                            </td>
                                            <td>{{$pedido->tempo_transp}}</td>
                                            <td>{{$pedido->ativ_dest ?? ""}}</td>
                                            <td>{{$pedido->ativ_crcn ?? ""}}</td>
                                            <td>{{$pedido->ativ_eos_frasco ?? ""}}</td>
                                            <td>{{$pedido->vol_frasco ?? ""}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br><br>
                        </div>

                        <div class="container mb-5">
                            <table class="table table-bordered">
                                <thead class="table-orange text-center">
                                    <tr>
                                        <th colspan="3" scope="colgroup"> Parâmetros Gerais </th>
                                        <th colspan="3" scope="colgroup"> Cíclotron </th>
                                        <th colspan="3" scope="colgroup"> Síntese </th>
                                    </tr>
                                </thead>
                                <tbody class="text-start">
                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Data da Produção </td>
                                        
                                        <td style="text-align: center; vertical-align: middle;">
                                            {{ old('data_producao', $parametros['data_producao']) }}
                                        </td>
                                        
                                        <td></td>
                
                                        <!-- Cíclotron -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Início </td>
                                        <td class="text-end">{{session()->get('inicio_ciclotron') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>
                                        
                                        <!-- Síntese -->    
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Início </td>
                                        <td class="text-end">{{session()->get('inicio_sintese') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>
                                    </tr>
                
                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Horário de Saída </td>
                                        <td class="text-end" style="background-color: #eab1a0;"><input class="bg-transparent border-0" id="hora_saida" type="time" name="hora_saida" value="{{old('hora_saida', $parametros['hora_saida'])}}" required></td>
                                        <td class="text-start">{{'h'}}</td>

                                        <!-- Cíclotron -->    
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Fim </td>
                                        <td class="text-end">{{session()->get('fim_ciclotron') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>

                                        <!-- Síntese -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Fim </td>
                                        <td class="text-end">{{session()->get('fim_sintese') ?? '-'}}</td> 
                                        <td class="text-start">{{'h'}}</td>
                                    </tr>
                                
                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Atividade por Dose </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="ativ_dose" type="number" name="ativ_dose" value="{{old('ativ_dose', $parametros['ativ_dose'])}}" required></td>
                                        <td class="text-start">{{"mCi"}}</td>

                                        <!-- Cíclotron -->    
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Duração </td>
                                        <td class="text-end">{{session()->get('dur_ciclotron') ?? '-'}}</td>
                                        <td class="text-start">{{"h"}}</td>

                                        <!-- Síntese -->   
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Duração </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="tempo_sintese" type="number" name="tempo_sintese" value="{{old('tempo_sintese', $parametros['tempo_sintese'])}}" required></td>
                                        <td class="text-start">{{"min"}}</td>
                                    </tr>
                                
                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Volume Frascos C.Q. </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="vol_max_cq" type="number" name="vol_max_cq" value="{{old('vol_max_cq', $parametros['vol_max_cq'])}}" required></td>
                                        <td class="text-start">{{"ml"}}</td>


                                        <!-- Cíclotron -->    
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Rendimento Típico do Cíclotron </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end bg-transparent border-0"  id="rend_tip_ciclotron" type="number" name="rend_tip_ciclotron" value="{{old('rend_tip_ciclotron', $parametros['rend_tip_ciclotron'])}}" required></td> 
                                        <td class="text-start">{{"mCi/µAsat"}}</td>

                                        <!-- Síntese -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Rend. da Síntese </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="rend_sintese" type="number" name="rend_sintese" value="{{old('rend_sintese', $parametros['rend_sintese'])}}" required></td>
                                        <td class="text-start">{{"%"}}</td>
                                    </tr>

                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Tempo Entre Exames </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="tempo_exames" type="number" name="tempo_exames" value="{{old('tempo_exames', $parametros['tempo_exames'])}}" required></td>
                                        <td class="text-start">{{"min"}}</td>

                                        <!-- Cíclotron -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Corrente Alvo </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="corrente_alvo" type="number" name="corrente_alvo" value="{{old('corrente_alvo', $parametros['corrente_alvo'])}}" required></td>
                                        <td class="text-start">{{"µA"}}</td>

                                        <!-- Síntese -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Volume EOS </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="vol_eos" type="number" name="vol_eos" value="{{old('vol_eos', $parametros['vol_eos'])}}" required></td> 
                                        <td class="text-start">{{"ml"}}</td>
                                    </tr>

                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Tempo de Expedição </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="tempo_exped" type="number" name="tempo_exped" value="{{old('tempo_exped', $parametros['tempo_exped'])}}" required></td>
                                        <td class="text-start">{{"min"}}</td>

                                        <!-- Cíclotron -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Fator de Segurança </td>
                                        <td style="background-color: #eab1a0;"><input class="text-end w-100 bg-transparent border-0" id="fator_seguranca" type="number" name="fator_seguranca" value="{{old('fator_seguranca', 0)}}" required></td>
                                        <td class="text-start">{{"%"}}</td>

                                        <!-- Síntese -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Ativ. Esp. </td>
                                        <td class="text-end">{{session()->get('ativ_esp') ?? '-'}}</td>
                                        <td class="text-start">{{"mCi/ml"}}</td>
                                    </tr>

                                    <tr>
                                        <!-- Parâmetros Gerais -->
                                        <td colspan="1"></td>
                                        <td colspan="1"></td>
                                        <td colspan="1"></td>

                                        <!-- Cíclotron -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Atividade EOB </td>
                                        <td class="text-end">{{session()->get('eob') ?? '-'}}</td>
                                        <td class="text-start">{{"mCi"}}</td>

                                        <!-- Síntese -->
                                        <td class="text-white text-center" style="--bs-table-color: #fff;--bs-table-bg: #f2714b;"> Atividade EOS </td>
                                        <td class="text-end">{{session()->get('eos') ?? '-'}}</td>
                                        <td class="text-start">{{"mCi"}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <br>
                        <div class="container">
                            <div class="flex justify-content-center">
                                <button name="action" value="calculate" class="mb-4 btn btn-orange">
                                    {{'Calcular'}}
                                </button>
                            </div>
                            @if (Session::has('eob'))
                                <div class="flex justify-content-center">
                                    <button name="action" value="save" class="mb-4 btn btn-orange">
                                        {{'Salvar Planejamento'}}
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif

                </form>
            </div>
        </div>
    </div>
</x-app-layout>