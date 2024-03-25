@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Produtos';
        $path = 'produtos/cadastrar';
        $columns = ['#', 'Nome', 'Descrição', 'Tipo', 'Data Emissão', 'Quantidade Aceitável', 'Quantidade Mínima'];
        $indexes = ['id', 'nome', 'descricao', 'tipo', 'data_emissao', 'qtd_aceitavel', 'qtd_minima'];
        $infos = $produtos;
    @endphp
@endsection