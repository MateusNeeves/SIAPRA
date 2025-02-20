<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="m-5 text-gray-900 text-center h3">
                Selecione o Relatório
            </div>
            
            <div class="container flex flex-col items-center">
                <form method="GET" class="w-full max-w-md" action="{{route('relatorios.generate')}}">
                    <div class="mb-4">
                        <label class="block text-gray-700">Escolha o tipo de relatório:</label>
                        <select name="tipo_relatorio" id="tipo_relatorio" class="w-full mt-2 p-2 border rounded">
                            <option value="" selected disabled>Selecione um relatório...</option>
                            <option value="itens_a_vencer">Itens a vencer</option>
                            <option value="inventario">Inventário do almoxarifado</option>
                        </select>
                        
                    </div>
                    
                    <div id="meses_container" class="mb-4 hidden">
                        <label class="block text-gray-700">Itens a vencer nos próximos 
                            <input type="number" min="1" max="12" name="meses" id="meses" class="p-2 border rounded" value="1" style="width: 50px">
                            meses
                        </label>
                    </div>
                    <div class="flex justify-content-center my-4">

                        <button disabled id="submitBtn" class="btn btn-orange bg-gradient me-2" >
                            Gerar Relatório
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tipo_relatorio').addEventListener('change', function() {
            let mesesContainer = document.getElementById('meses_container');
            if (this.value === 'itens_a_vencer') {
                mesesContainer.classList.remove('hidden');
            } else {
                mesesContainer.classList.add('hidden');
            }

            if (this.value) {
                document.getElementById('submitBtn').removeAttribute('disabled');
            } else {
                document.getElementById('submitBtn').setAttribute('disabled', 'disabled');
            }
        });
    </script>
</x-app-layout>