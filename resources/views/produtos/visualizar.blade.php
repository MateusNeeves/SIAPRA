@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Produtos';
        $path = 'produtos';
        $columns = ['#', 'Nome', 'Descrição', 'Tipo','Quantidade Aceitável', 'Quantidade Mínima'];
        $indexes = ['id', 'nome', 'descricao', 'tipo', 'qtd_aceitavel', 'qtd_minima'];
        $infos = $produtos;
    @endphp
@endsection