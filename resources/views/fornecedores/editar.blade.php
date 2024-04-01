@extends('layouts.editar')

@section('variables')
    @php
        $title = "Editando Fornecedor '".$fornecedor->nome . "'";
        $path = 'fornecedores';
        $id = $fornecedor->id;

    @endphp
@endsection

@section('content')
    <!-- Nome -->
    <div class="mt-4">
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" value="{{old('nome') ?? $fornecedor->nome}}" required/>
    </div>

    <!-- Endereeço -->
    <div class="mt-4">
        <x-input-label :value="__('Endereço *')" />
        <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" value="{{old('endereco') ?? $fornecedor->endereco}}" required/>
    </div>

    <!-- País -->
    <div class="mt-4">
        <x-input-label :value="__('País *')" />
        <select id="pais" class="block mt-1 w-full border rounded" name="pais" required>
            @foreach ($paises as $pais)
                <option value="{{$pais['nome']}}" {{(old('pais') ?? $fornecedor->pais) == $pais['nome'] ? "selected" : "" }}> {{$pais['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Nome do Contato -->
    <div class="mt-4">
        <x-input-label :value="__('Nome do Contato')" />
        <x-text-input id="nome_contato" class="block mt-1 w-full" type="text" name="nome_contato" value="{{old('nome_contato') ?? $fornecedor->nome_contato}}"/>
    </div>

    <!-- Telefone -->
    <div class="mt-4">
        <x-input-label :value="__('Telefone *')" />
        <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" value="{{old('telefone') ?? $fornecedor->telefone}}" required/>
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{old('email') ?? $fornecedor->email}}"/>
    </div>

    <!-- Site -->
    <div class="mt-4">
        <x-input-label :value="__('Site')" />
        <x-text-input id="site" class="block mt-1 w-full" type="text" name="site" value="{{old('site') ?? $fornecedor->site}}"/>
    </div>

    <!-- CNPJ -->
    <div class="mt-4">
        <x-input-label :value="__('CNPJ')" />
        <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" value="{{old('cnpj') ?? $fornecedor->cnpj}}"/>
    </div>
@endsection