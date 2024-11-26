<x-app-layout>
    <div class="w-100 py-16">
        <div style="width:fit-content" class="mx-auto sm:px-6 lg:px-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container justify-content-center">
                <div class="m-5 text-gray-900 text-center h3">
                    Pesquisa dos Logs
                </div>

                <form method="get" action="{{route('logs.view')}}">
                    <!-- Nome do Usuário -->
                    <div class="mt-4">
                        <x-input-label :value="__('Nome do Usuário')" />
                        <x-text-input id="user" class="block mt-1 w-full" type="text" name="user" :value="old('user')"/>
                    </div>

                    <!-- Busca Geral -->
                    <div class="mt-4">
                        <x-input-label :value="__('Busca Geral')" />
                        <x-text-input id="generalSearch" class="block mt-1 w-full" type="text" name="generalSearch" :value="old('generalSearch')"/>
                    </div>

                    <!-- Data Inicial -->
                    <div class="mt-4">
                        <x-input-label :value="__('Data Inicial *')" />
                        <x-text-input id="startDate" class="block mt-1 w-full" type="date" name="startDate" :value="old('startDate')" required/>
                    </div>

                    <!-- Data Final -->
                    <div class="mt-4">
                        <x-input-label :value="__('Data Final *')" />
                        <x-text-input id="endDate" class="block mt-1 w-full" type="date" name="endDate" :value="old('endDate')" required/>
                    </div>

                    <!-- Ações -->
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <x-input-label :value="__('Ações *')" />
                            <x-input-label :value="__('(Selecione ao menos 1 opção)')" />
                        </div>
                        <select class="multiple-select block mt-1 w-full border rounded" name="acoes[]" multiple >
                            @foreach ($acoes as $acao)
                                <option value="{{$acao}}" {{in_array($acao, old('acoes') ?? []) ? "selected" : ""}} > {{$acao}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="my-4 flex justify-content-end">
                        <button type="submit" class="btn btn-orange">Pesquisar</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</x-app-layout>