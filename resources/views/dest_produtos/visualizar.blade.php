@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Destinos dos Produtos', 'Destino do Produto'];
        $path = 'dest_produtos';
        $columns = ['#', 'Nome'];
        $indexes = ['id', 'nome'];
        $infos = $dest_produtos;
    @endphp
@endsection


@section('content')
    @php
        $dest_produto = Session::get('dest_produto') ?? null;
    @endphp
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $dest_produto->nome ?? '')" required/>
    </div>
@endsection