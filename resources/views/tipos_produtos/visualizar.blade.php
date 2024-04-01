@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Tipos de Produtos';
        $path = 'tipos_produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Sigla'];
        $indexes = ['id', 'nome', 'descricao', 'sigla'];
        $infos = $tipos_produtos;
    @endphp
@endsection