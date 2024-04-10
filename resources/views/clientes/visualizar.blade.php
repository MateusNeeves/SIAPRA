@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Clientes';
        $path = 'clientes';
        $columns = ['#', 'CNPJ', 'Razão Social', 'Nome Fantasia', 'Logradouro', 'Complemento', 'Estado', 'Cidade', 'Bairro', 'CEP', 'Tempo de Transporte'];
        $indexes = ['id', 'cnpj', 'razao_social', 'nome_fantasia', 'end_logradouro', 'end_complemento', 'estado', 'cidade', 'bairro', 'cep', 'tempo_transp'];
        $editables = ['cnpj', 'razao_social', 'nome_fantasia', 'end_logradouro', 'end_complemento', 'estado', 'cidade', 'bairro', 'cep', 'tempo_transp'];
        $infos = $clientes;
    @endphp
@endsection

@section('content')
    <!-- CNPJ -->
    <div>
        <x-input-label :value="__('CNPJ *')" />
        <x-text-input id="cnpj" class="input block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj')" required autofocus/>
    </div>

    <!-- Razão Social -->
    <div class="mt-4">
        <x-input-label :value="__('Razão Social *')" />
        <x-text-input id="razao_social" class="input block mt-1 w-full" type="text" name="razao_social" :value="old('razao_social')" required/>
    </div>

    <!-- Nome Fantasia -->
    <div class="mt-4">
        <x-input-label :value="__('Nome Fantasia *')" />
        <x-text-input id="nome_fantasia" class="input block mt-1 w-full" type="text" name="nome_fantasia" :value="old('nome_fantasia')" required/>
    </div>

    <!-- Logradouro -->
    <div class="mt-4">
        <x-input-label :value="__('Logradouro *')" />
        <x-text-input id="end_logradouro" class="input block mt-1 w-full" type="text" name="end_logradouro" :value="old('end_logradouro')" required/>
    </div>

    <!-- Complemento -->
    <div class="mt-4">
        <x-input-label :value="__('Complemento')" />
        <x-text-input id="end_complemento" class="input block mt-1 w-full" type="text" name="end_complemento" :value="old('end_complemento')"/>
    </div>

    <!-- Estado -->
    <div class="mt-4">
        <x-input-label :value="__('Estado *')" />
        <x-text-input id="estado" class="input block mt-1 w-full" type="text" name="estado" :value="old('estado')" required/>
    </div>

    <!-- Cidade -->
    <div class="mt-4">
        <x-input-label :value="__('Cidade *')" />
        <x-text-input id="cidade" class="input block mt-1 w-full" type="text" name="cidade" :value="old('cidade')" required/>
    </div>

    <!-- Bairro -->
    <div class="mt-4">
        <x-input-label :value="__('Bairro *')" />
        <x-text-input id="bairro" class="input block mt-1 w-full" type="text" name="bairro" :value="old('bairro')" required/>
    </div>

    <!-- CEP -->
    <div class="mt-4">
        <x-input-label :value="__('CEP *')" />
        <x-text-input id="cep" class="input block mt-1 w-full" type="text" name="cep" :value="old('cep')" required/>
    </div>

    <!-- Tempo de Transporte -->
    <div class="mt-4">
        <x-input-label :value="__('Tempo de Transporte (minutos) *')" />
        <x-text-input id="tempo_transp" class="input block mt-1 w-full" type="number" name="tempo_transp" :value="old('tempo_transp')" required/>
    </div>
@endsection