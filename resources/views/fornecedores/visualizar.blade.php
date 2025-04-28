@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Fornecedores', 'Fornecedor'];
        $path = 'fornecedores';
        $columns = ['#', 'Nome', 'País', 'CNPJ', 'Endereço', 'Contato', 'Telefone', 'Email', 'Site', 'Inscrição Estadual', 'Inscrição Municipal'];
        $indexes = ['id', 'nome', 'pais', 'cnpj', 'endereco', 'nome_contato', 'telefone', 'email', 'site', 'inscricao_estadual', 'inscricao_municipal'];
        $infos = $fornecedores;
    @endphp
@endsection


@section('content')
    @php
        $fornecedor = Session::get('fornecedor') ?? null;
    @endphp
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $fornecedor->nome ?? '')" required/>
    </div>

    <!-- País -->
    <div class="mt-4">
        <x-input-label :value="__('País *')" />
        <select id="pais" class="block mt-1 w-full border rounded" name="pais" required onchange="toggleBrasilFields()">
            <option value="" hidden></option>
            @foreach ($paises as $pais)
                <option value="{{$pais['nome']}}" {{$pais['nome'] == (old('pais') ?? $fornecedor->pais ?? "") ? "selected" : ""}}>{{$pais['nome']}} </option>
            @endforeach
        </select>
    </div>

    <script>
        function toggleBrasilFields() {
            // Obtém o valor selecionado do país
            var pais = document.getElementById('pais').value;
            
            // Campos a serem mostrados ou ocultados
            var cnpjField = document.getElementById('cnpj-field');
            var cepField = document.getElementById('cep-field');
            var numeroField = document.getElementById('numero-field');
            var complementoField = document.getElementById('complemento-field');
            var cidadeField = document.getElementById('cidade-field');
            var estadoField = document.getElementById('estado-field');
            var inscricao_estadualField = document.getElementById('inscricao_estadual-field');
            var inscricao_municipalField = document.getElementById('inscricao_municipal-field');
            
            // Inputs individuais para adicionar/remover o 'required'
            var cnpjInput = document.getElementById('cnpj');
            var cepInput = document.getElementById('cep');
            var numeroInput = document.getElementById('numero');
            var cidadeInput = document.getElementById('cidade');
            var estadoInput = document.getElementById('estado');
            
            // Se o país for "Brasil", exibe os campos e adiciona o 'required', caso contrário oculta e remove o 'required'
            if (pais === "BRASIL") {
                cnpjField.style.display = "block";
                cepField.style.display = "block";
                numeroField.style.display = "block";
                complementoField.style.display = "block";
                cidadeField.style.display = "block";
                estadoField.style.display = "block";
                inscricao_estadualField.style.display = "block";
                inscricao_municipalField.style.display = "block";
                
                cnpjInput.setAttribute('required', 'required');
                cepInput.setAttribute('required', 'required');
                numeroInput.setAttribute('required', 'required');
                cidadeInput.setAttribute('required', 'required');
                estadoInput.setAttribute('required', 'required');
            } else {
                cnpjField.style.display = "none";
                cepField.style.display = "none";
                numeroField.style.display = "none";
                complementoField.style.display = "none";
                cidadeField.style.display = "none";
                estadoField.style.display = "none";
                inscricao_estadualField.style.display = "none";
                inscricao_municipalField.style.display = "none";
                
                cnpjInput.removeAttribute('required');
                cepInput.removeAttribute('required');
                numeroInput.removeAttribute('required');
                cidadeInput.removeAttribute('required');
                estadoInput.removeAttribute('required');
            }
        }

        // Executa a função ao carregar a página para garantir que a exibição e atributos estejam corretos
        window.onload = function() {
            toggleBrasilFields();
        };
    </script>

    <!-- CNPJ -->
    <div id="cnpj-field" class="mt-4" style="display:none;">
        <div class="flex justify-between">
            <x-input-label :value="__('CNPJ *')" />
            <x-input-label :value="__('(Apenas Números)')" />
        </div>
        <x-text-input id="cnpj" class="block mt-1 w-full" type="text" maxlength="14" name="cnpj" :value="old('cnpj', $fornecedor->cnpj ?? '')" />
    </div>

    <!-- CEP -->
    <div id="cep-field" class="mt-4" style="display:none;">
        <div class="flex justify-between">
            <x-input-label :value="__('CEP *')" />
            <x-input-label :value="__('(Apenas Números)')" />
        </div>
        <x-text-input id="cep" class="block mt-1 w-full" type="text" maxlength="8" name="cep" :value="old('cep', $fornecedor->cep ?? '')" />
    </div>

    <!-- Endereço -->
    <div class="mt-4">
        <x-input-label :value="__('Endereço *')" />
        <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco', $fornecedor->endereco ?? '')" required/>
    </div>

    <!-- Número -->
    <div id="numero-field" class="mt-4" style="display:none;">
        <x-input-label :value="__('Número *')" />
        <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero" :value="old('numero', $fornecedor->numero ?? '')"/>
    </div>

    <!-- Complemento -->
    <div id="complemento-field" class="mt-4" style="display:none;">
        <x-input-label :value="__('Complemento')" />
        <x-text-input id="complemento" class="block mt-1 w-full" type="text" name="complemento" :value="old('complemento', $fornecedor->complemento ?? '')"/>
    </div>

    <!-- Cidade -->
    <div id="cidade-field" class="mt-4" style="display:none;">
        <x-input-label :value="__('Cidade *')" />
        <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade', $fornecedor->cidade ?? '')"/>
    </div>

    <!-- Estado -->
    <div id="estado-field" class="mt-4" style="display:none;">
        <x-input-label :value="__('Estado *')" />
        <select id="estado" class="block mt-1 w-full border rounded" name="estado">
            <option value="" hidden></option>
            @php 
                $estados = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MS', 'MT', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']; 
            @endphp
            @foreach ($estados as $estado)
                <option value="{{$estado}}" {{$estado == (old('estado') ?? $fornecedor->estado ?? "") ? "selected" : ""}}>{{$estado}} </option>
            @endforeach
        </select>
    </div>

    <!-- Nome do Contato -->
    <div class="mt-4">
        <x-input-label :value="__('Nome do Contato')" />
        <x-text-input id="nome_contato" class="block mt-1 w-full" type="text" name="nome_contato" :value="old('nome_contato', $fornecedor->nome_contato ?? '')"/>
    </div>

    <!-- Telefone -->
    <div class="mt-4">
        <div class="flex justify-between">
            <x-input-label :value="__('Telefone *')" />
            <x-input-label :value="__('(Apenas Números)')" />
        </div>
        <x-text-input id="telefone" class="block mt-1 w-full" type="text" maxlength="11" name="telefone" :value="old('telefone', $fornecedor->telefone ?? '')" required/>
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $fornecedor->email ?? '')"/>
    </div>

    <!-- Site -->
    <div class="mt-4">
        <x-input-label :value="__('Site')" />
        <x-text-input id="site" class="block mt-1 w-full" type="text" name="site" :value="old('site', $fornecedor->site ?? '')"/>
    </div>

    <!-- Inscrição Estadual -->
    <div id="inscricao_estadual-field" class="mt-4" style="display:none;">
        <x-input-label :value="__('Inscrição Estadual')" />
        <x-text-input id="inscricao_estadual" class="block mt-1 w-full" type="text" name="inscricao_estadual" maxlength="20" :value="old('inscricao_estadual', $fornecedor->inscricao_estadual ?? '')"/>
    </div>

    <!-- Inscrição Municipal -->
    <div id="inscricao_municipal-field" class="mt-4" style="display:none;">
        <x-input-label :value="__('Inscrição Municipal')" />
        <x-text-input id="inscricao_municipal" class="block mt-1 w-full" type="text" name="inscricao_municipal" maxlength="20" :value="old('inscricao_municipal', $fornecedor->inscricao_municipal ?? '')"/>
    </div>
@endsection