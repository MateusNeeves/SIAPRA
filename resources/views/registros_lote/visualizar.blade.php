<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Registros de Lote'}}
                </div>

                <form method="POST" action="{{route('registros_lote.make_pdf')}}">
                    @csrf
                    @if (array_intersect(['Admin', 'Produção', 'Farmacêutico'], Auth::user()->getClassNamesAttribute()))
                        <div class="flex justify-content-center mb-5">
                            <a class="btn btn-orange" href="{{route('registros_lote.register')}}">
                                {{ __('Criar/Continuar Registro de Lote') }}
                            </a>
                        </div>
                    @endif
                    <div class="flex justify-content-center mb-5">
                        <div class="position-relative" style="width: 220px">
                            <input class="btn-orange border rounded border-dark placeholder-visible pe-3"  type="text" id="datePicker" name="data_fabricacao" value="{{old('data_fabricacao')}}" placeholder="Selecionar Data" readonly required style="">
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
                            <button disabled class="btn btn-orange  ms-3" id="visualizar_button" style="width: 250px">
                                {{ __('Visualizar Registro de Lote') }}
                            </button>
                        </button>
                    </div>

                </form>

</x-app-layout>