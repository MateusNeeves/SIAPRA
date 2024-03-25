@extends('layouts.cadastrar')

@section('title', 'Cadastrar Fornecedor')

@section('path', 'cadastrar')

@section('content')
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus/>
    </div>

    <!-- Endereço -->
    <div class="mt-4">
        <x-input-label :value="__('Endereço *')" />
        <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco')" required/>
    </div>

    <!-- País -->
    <div class="mt-4">
        <x-input-label :value="__('País *')" />
        <select id="pais" class="block mt-1 w-full border rounded" name="pais" :value="old('pais')" required>
            <option value="" hidden></option>
            @foreach ($paises as $pais)
                <option value="{{$pais['nome']}}" {{old('pais') == $pais['nome'] ? "selected" : "" }}> {{$pais['nome']}} </option>
            @endforeach
        </select>
    </div>


    <!-- Nome do Contato -->
    <div class="mt-4">
        <x-input-label :value="__('Nome do Contato')" />
        <x-text-input id="nome_contato" class="block mt-1 w-full" type="text" name="nome_contato" :value="old('nome_contato')"/>
    </div>

    <!-- Telefone -->
    <div class="mt-4">
        <x-input-label :value="__('Telefone *')" />
        <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone')" required/>
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"/>
    </div>

    <!-- Site -->
    <div class="mt-4">
        <x-input-label :value="__('Site')" />
        <x-text-input id="site" class="block mt-1 w-full" type="text" name="site" :value="old('site')"/>
    </div>

    <!-- CNPJ -->
    <div class="mt-4">
        <x-input-label :value="__('CNPJ')" />
        <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj')" />
    </div>
@endsection