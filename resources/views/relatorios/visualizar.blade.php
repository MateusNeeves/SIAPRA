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

                    <div id="ano_container" class="mb-4 hidden">
                        <label class="block text-gray-700">Ano: 
                            <input type="number" value="{{ date('Y') }}" max="{{ date('Y') }}" min="1970" name="ano" id="ano" class="p-2 border rounded"  style="width: 100px">
                        </label>
                    </div>

                    <div id="mes_container" class="mb-4 hidden">
                        <label class="block text-gray-700">Mês:</label>
                        <select name="mes" id="mes" class="w-full mt-2 p-2 border rounded">
                            <option value="todos">Todos</option>
                            <option value="1">Janeiro</option>
                            <option value="2">Fevereiro</option>
                            <option value="3">Março</option>
                            <option value="4">Abril</option>
                            <option value="5">Maio</option>
                            <option value="6">Junho</option>
                            <option value="7">Julho</option>
                            <option value="8">Agosto</option>
                            <option value="9">Setembro</option>
                            <option value="10">Outubro</option>
                            <option value="11">Novembro</option>
                            <option value="12">Dezembro</option>
                        </select>
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
            let anoContainer = document.getElementById('ano_container');
            let mesContainer = document.getElementById('mes_container');

            if (this.value === 'itens_a_vencer') {
                mesesContainer.classList.remove('hidden');
                anoContainer.classList.add('hidden');
                mesContainer.classList.add('hidden');
            } 
            else if (this.value === 'inventario') {
                mesesContainer.classList.add('hidden');
                anoContainer.classList.remove('hidden');
                mesContainer.classList.remove('hidden');
            } 
            else {
                mesesContainer.classList.add('hidden');
                anoContainer.classList.add('hidden');
                mesContainer.classList.add('hidden');
            }

            if (this.value) {
                document.getElementById('submitBtn').removeAttribute('disabled');
            } else {
                document.getElementById('submitBtn').setAttribute('disabled', 'disabled');
            }
        });
    </script>
</x-app-layout>