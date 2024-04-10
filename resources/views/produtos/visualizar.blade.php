@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Produtos';
        $path = 'produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Tipo','Quantidade Aceitável', 'Quantidade Mínima'];
        $indexes = ['id', 'nome', 'descricao', 'tipo', 'qtd_aceitavel', 'qtd_minima'];
        $editables = ['nome', 'descricao', 'tipo', 'qtd_aceitavel', 'qtd_minima'];
        $infos = $produtos;
    @endphp
@endsection


@section('content')
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="input block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus/>
    </div>

    <!-- Descricao -->
    <div class="mt-4">
        <x-input-label :value="__('Descricao *')" />
        <x-text-input id="descricao" class="input block mt-1 w-full" type="text" name="descricao" :value="old('descricao')" required autofocus/>
    </div>

    <!-- Tipo do Produto -->
    <div class="mt-4">
        <x-input-label :value="__('Tipo do Produto *')" />
        <select id="tipo" class="input select block mt-1 w-full border rounded" name="tipo" :value="old('tipo')" required autofocus>
            <option value="" hidden></option>
            @foreach ($tipos as $tipo)
                <option value="{{$tipo['nome']}}"> {{$tipo['nome']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Quantidade Aceitável -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Aceitável *')" />
        <x-text-input id="qtd_aceitavel" class="input block mt-1 w-full" type="number" name="qtd_aceitavel" :value="old('qtd_aceitavel')" required/>
    </div>

    <!-- Quantidade Mínima -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Mínima *')" />
        <x-text-input id="qtd_minima" class="input block mt-1 w-full" type="number" name="qtd_minima" :value="old('qtd_minima')" required/>
    </div>
@endsection