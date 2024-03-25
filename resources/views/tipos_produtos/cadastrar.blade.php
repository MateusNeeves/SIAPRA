@extends('layouts.cadastrar')

@section('title', 'Cadastrar Tipo de Produto')

@section('path', 'cadastrar')

@section('content')
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus/>
    </div>

    <!-- Descrição -->
    <div class="mt-4">
        <x-input-label :value="__('Descrição')" />
        <x-text-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao')"/>
    </div>

    <!-- Sigla -->
    <div class="mt-4">
        <x-input-label :value="__('Sigla *')" />
        <x-text-input id="sigla" class="block mt-1 w-full" type="text" name="sigla" :value="old('sigla')" required/>
    </div>
@endsection