@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Produtos';
        $path = 'produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Tipo', 'Fabricantes', 'Fornecedores', 'Quantidade Aceitável', 'Quantidade Mínima'];
        $indexes = ['id', 'nome', 'descricao', 'tipo', 'fabricantes', 'fornecedores', 'qtd_aceitavel', 'qtd_minima'];
        $infos = $produtos;
    @endphp
@endsection

@section('content')
    @php
        $produto = Session::get('produto') ?? null;
        $fabs = Session::get('fabricantes') ?? null;
        $forns = Session::get('fornecedores') ?? null;
    @endphp

    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $produto->nome ?? '')" required/>
    </div>

    <!-- Descricao -->
    <div class="mt-4">
        <x-input-label :value="__('Descricao *')" />
        <x-text-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao', $produto->descricao ?? '')" required/>
    </div>

    <!-- Tipo do Produto -->
    <div class="mt-4">
        <x-input-label :value="__('Tipo do Produto *')" />
        <select id="tipo" class="block mt-1 w-full border rounded" name="tipo" required>
            <option value="" hidden></option>
            @foreach ($tipos as $tipo)
                <option value="{{$tipo['nome']}}" {{$tipo['nome'] == (old('tipo') ?? $produto->tipo ?? "") ? "selected" : ""}}> {{$tipo['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Fabricante -->
    <div class="mt-4">
        <x-input-label :value="__('Fabricantes')" />
        <select class="multiple-select block mt-1 w-full border rounded" name="fabricantes[]" multiple >
            @foreach ($fabricantes as $fabricante)
                <option value="{{$fabricante['nome']}}" {{in_array($fabricante['nome'], $fabs ?? old('fabricantes') ?? []) ? "selected" : ""}} > {{$fabricante['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Fornecedor -->
    <div class="mt-4">
        <x-input-label :value="__('Fornecedores')" />
        <select class="multiple-select block mt-1 w-full border rounded" name="fornecedores[]" multiple :value="old('fornecedores')">
            @foreach ($fornecedores as $fornecedor)
                <option value="{{$fornecedor['nome']}}" {{in_array($fornecedor['nome'], $forns ?? old('fornecedores') ?? []) ? "selected" : ""}}> {{$fornecedor['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Quantidade Aceitável -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Aceitável *')" />
        <x-text-input id="qtd_aceitavel" class="block mt-1 w-full" type="number" name="qtd_aceitavel" :value="old('qtd_aceitavel', $produto->qtd_aceitavel ?? '')" required/>
    </div>

    <!-- Quantidade Mínima -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Mínima *')" />
        <x-text-input id="qtd_minima" class="block mt-1 w-full" type="number" name="qtd_minima" :value="old('qtd_minima', $produto->qtd_minima ?? '')" required/>
    </div>

@endsection