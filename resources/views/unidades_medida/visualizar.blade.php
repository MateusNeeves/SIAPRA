@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Unidades de Medida', 'Unidade de Medida'];
        $path = 'unidades_medida';
        $columns = ['#', 'Nome', 'Sigla'];
        $indexes = ['id', 'nome', 'sigla'];
        $infos = $unidades_medida;
    @endphp
@endsection


@section('content')
    @php
        $unidade_medida = Session::get('unidade_medida') ?? null;
    @endphp
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $unidade_medida->nome ?? '')" required/>
    </div>

    <!-- Sigla -->
    <div class="mt-4">
        <x-input-label :value="__('Sigla *')" />
        <x-text-input id="sigla" class="block mt-1 w-full" type="text" name="sigla" :value="old('sigla', $unidade_medida->sigla ?? '')" required/>
    </div>
@endsection