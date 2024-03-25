@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Pedidos';
        $path = 'pedidos/cadastrar';
        $columns = ['#', 'Cliente', 'Usuário', 'Qtd Doses', 'Data Solicitação', 'Data Entrega'];
        $indexes = ['id', 'nome_fantasia', 'username', 'qtd_doses', 'data_solicitacao', 'data_entrega'];
        $infos = $pedidos;
    @endphp
@endsection