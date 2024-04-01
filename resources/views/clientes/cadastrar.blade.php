@extends('layouts.cadastrar')

@section('variables')
    @php
        $title =  'Cadastrar Cliente';
        $path = 'clientes';
    @endphp
@endsection

@section('content')
    <!-- CNPJ -->
    <div>
        <x-input-label :value="__('CNPJ *')" />
        <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj')" required autofocus/>
    </div>

    <!-- Razão Social -->
    <div class="mt-4">
        <x-input-label :value="__('Razão Social *')" />
        <x-text-input id="razao_social" class="block mt-1 w-full" type="text" name="razao_social" :value="old('razao_social')" required/>
    </div>

    <!-- Nome Fantasia -->
    <div class="mt-4">
        <x-input-label :value="__('Nome Fantasia *')" />
        <x-text-input id="nome_fantasia" class="block mt-1 w-full" type="text" name="nome_fantasia" :value="old('nome_fantasia')" required/>
    </div>

    <!-- Logradouro -->
    <div class="mt-4">
        <x-input-label :value="__('Logradouro *')" />
        <x-text-input id="end_logradouro" class="block mt-1 w-full" type="text" name="end_logradouro" :value="old('end_logradouro')" required/>
    </div>

    <!-- Complemento -->
    <div class="mt-4">
        <x-input-label :value="__('Complemento')" />
        <x-text-input id="end_complemento" class="block mt-1 w-full" type="text" name="end_complemento" :value="old('end_complemento')"/>
    </div>

    <!-- Estado -->
    <div class="mt-4">
        <x-input-label :value="__('Estado *')" />
        <x-text-input id="estado" class="block mt-1 w-full" type="text" name="estado" :value="old('estado')" required/>
    </div>

    <!-- Cidade -->
    <div class="mt-4">
        <x-input-label :value="__('Cidade *')" />
        <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade')" required/>
    </div>

    <!-- Bairro -->
    <div class="mt-4">
        <x-input-label :value="__('Bairro *')" />
        <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro')" required/>
    </div>

    <!-- CEP -->
    <div class="mt-4">
        <x-input-label :value="__('CEP *')" />
        <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" :value="old('cep')" required/>
    </div>

    <!-- Tempo de Transporte -->
    <div class="mt-4">
        <x-input-label :value="__('Tempo de Transporte (minutos) *')" />
        <x-text-input id="tempo_transp" class="block mt-1 w-full" type="number" name="tempo_transp" :value="old('tempo_transp')" required/>
    </div>
@endsection