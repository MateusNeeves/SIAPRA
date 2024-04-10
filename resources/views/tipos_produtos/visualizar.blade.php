@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Tipos de Produtos';
        $path = 'tipos_produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Sigla'];
        $indexes = ['id', 'nome', 'descricao', 'sigla'];
        $editables = ['nome', 'descricao', 'sigla'];
        $infos = $tipos_produtos;
    @endphp
@endsection


@section('content')
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="input block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus/>
    </div>

    <!-- Descrição -->
    <div class="mt-4">
        <x-input-label :value="__('Descrição')" />
        <x-text-input id="descricao" class="input block mt-1 w-full" type="text" name="descricao" :value="old('descricao')"/>
    </div>

    <!-- Sigla -->
    <div class="mt-4">
        <x-input-label :value="__('Sigla *')" />
        <x-text-input id="sigla" class="input block mt-1 w-full" type="text" name="sigla" :value="old('sigla')" required/>
    </div>
@endsection