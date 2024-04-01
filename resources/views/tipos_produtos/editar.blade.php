@extends('layouts.editar')

@section('variables')
    @php
        $title = "Editando Tipo de Produto '".$tipo_produto->nome . "'";
        $path = 'tipos_produtos';
        $id = $tipo_produto->id;

    @endphp
@endsection

@section('content')
    <!-- Nome -->
    <div class="mt-4">
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" value="{{old('nome') ?? $tipo_produto->nome}}" required/>
    </div>

    <!-- Descrição -->
    <div class="mt-4">
        <x-input-label :value="__('Descrição')" />
        <x-text-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" value="{{old('descricao') ?? $tipo_produto->descricao}}"/>
    </div>

    <!-- Sigla -->
    <div class="mt-4">
        <x-input-label :value="__('Sigla *')" />
        <x-text-input id="sigla" class="block mt-1 w-full" type="text" name="sigla" value="{{old('sigla') ?? $tipo_produto->sigla}}" required/>
    </div>
@endsection