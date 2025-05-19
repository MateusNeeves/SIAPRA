<x-app-layout>
    <div class="w-100 py-16">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    {{'Registros de Lote'}}
                </div>

                <form method="GET" action="{{route('registros_lote.register')}}">
                    <div class="flex justify-content-center align-items-center mb-4">
                        <label class="me-4"> Selecione a Data de Fabricação do Lote: </label>
                        <div class="position-relative" style="width: 220px">
                            <input class="btn-orange border rounded border-dark placeholder-visible pe-3" type="date" id="data_fabricacao" name="data_fabricacao" value="{{ old('data_fabricacao') }}" required>
                        </div>

                    </div>
                    <div class="flex justify-content-center align-items-center mb-4">
                        <!-- Select para IDs dos lotes filtrados -->
                        <select id="loteSelect" name="loteSelect" class="form-select ms-3" style="width: 200px" disabled>
                            <option hidden value="">Selecione um Lote</option>
                        </select>
                    </div>
                    <div class="flex justify-content-center align-items-center mb-4">
                        <button disabled class="btn btn-orange ms-3" id="visualizar_button" style="width: 250px">
                            {{ __('Visualizar Registro de Lote') }}
                        </button>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Lotes vindos do backend
                            const lotes = @json($lotes);
                            const datePicker = document.getElementById('data_fabricacao');
                            const visualizarButton = document.getElementById('visualizar_button');
                            const loteSelect = document.getElementById('loteSelect');

                            // Inicialmente desabilita botão e select
                            visualizarButton.disabled = true;
                            loteSelect.disabled = true;

                            // Evento ao mudar a data
                            datePicker.addEventListener('change', function () {
                                const selectedDate = datePicker.value;
                                loteSelect.innerHTML = '<option hidden value="">Selecione um Lote</option>'; // limpa

                                if (selectedDate) {
                                    // Filtra os lotes pela data selecionada
                                    const lotesFiltrados = lotes.filter(lote => lote.data_producao === selectedDate);

                                    // Preenche o select com os IDs dos lotes
                                    lotesFiltrados.forEach(lote => {
                                        const option = document.createElement('option');
                                        option.value = lote.lote;
                                        option.textContent = lote.lote;
                                        loteSelect.appendChild(option);
                                    });

                                    // Ativa o select se houver lotes, mas não ativa o botão ainda
                                    loteSelect.disabled = lotesFiltrados.length === 0;
                                    visualizarButton.disabled = true;
                                } else {
                                    loteSelect.disabled = true;
                                    visualizarButton.disabled = true;
                                }
                            });

                            // Evento ao selecionar um lote
                            loteSelect.addEventListener('change', function () {
                                // Habilita o botão apenas se um valor válido for selecionado
                                visualizarButton.disabled = loteSelect.value === "";
                            });
                        });
                    </script>


                </form>

</x-app-layout>