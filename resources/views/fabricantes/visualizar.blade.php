@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Fabricantes', 'Fabricante'];
        $path = 'fabricantes';
        $columns = ['#', 'Nome', 'Endereço', 'País', 'Contato', 'Telefone', 'Email', 'Site', 'CNPJ'];
        $indexes = ['id', 'nome', 'endereco', 'pais', 'nome_contato', 'telefone', 'email', 'site', 'cnpj'];
        $infos = $fabricantes;
    @endphp
@endsection

@section('content')
    @php
        $fabricante = Session::get('fabricante') ?? null;
    @endphp

    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $fabricante->nome ?? '')" required autofocus/>
    </div>

    <!-- Endereço -->
    <div class="mt-4">
        <x-input-label :value="__('Endereço *')" />
        <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco', $fabricante->endereco ?? '')" required/>
    </div>

    <!-- País -->
    <div class="mt-4">
        <x-input-label :value="__('País *')" />
        <select id="pais" class="block mt-1 w-full border rounded" name="pais" required>
            <option value="" hidden></option>
            @foreach ($paises as $pais)
                <option value="{{$pais['nome']}}" {{$pais['nome'] == (old('pais') ?? $fabricante->pais ?? "") ? "selected" : ""}}>{{$pais['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Nome do Contato -->
    <div class="mt-4">
        <x-input-label :value="__('Nome do Contato')" />
        <x-text-input id="nome_contato" class="block mt-1 w-full" type="text" name="nome_contato" :value="old('nome_contato', $fabricante->nome_contato ?? '')"/>
    </div>

    <!-- Telefone -->
    <div class="mt-4">
        <x-input-label :value="__('Telefone *')" />
        <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone', $fabricante->telefone ?? '')" required/>
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $fabricante->email ?? '')"/>
    </div>

    <!-- Site -->
    <div class="mt-4">
        <x-input-label :value="__('Site')" />
        <x-text-input id="site" class="block mt-1 w-full" type="text" name="site" :value="old('site', $fabricante->site ?? '')"/>
    </div>

    <!-- CNPJ -->
    <div class="mt-4">
        <x-input-label :value="__('CNPJ')" />
        <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj', $fabricante->cnpj ?? '')" />
    </div>
@endsection