@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Tipos de Produtos', 'Tipo de Produto'];
        $path = 'tipos_produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Sigla'];
        $indexes = ['id', 'nome', 'descricao', 'sigla'];
        $infos = $tipos_produtos;
    @endphp
@endsection


@section('content')
    @php
        $tipo_produto = Session::get('tipo_produto') ?? null;
    @endphp
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $tipo_produto->nome ?? '')" required/>
    </div>

    <!-- Descrição -->
    <div class="mt-4">
        <x-input-label :value="__('Descrição')" />
        <x-text-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao', $tipo_produto->descricao ?? '')"/>
    </div>

    <!-- Sigla -->
    <div class="mt-4">
        <x-input-label :value="__('Sigla *')" />
        <x-text-input id="sigla" class="block mt-1 w-full" type="text" name="sigla" :value="old('sigla', $tipo_produto->sigla ?? '')" required/>
    </div>
@endsection