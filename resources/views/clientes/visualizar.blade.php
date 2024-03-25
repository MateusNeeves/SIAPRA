@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Clientes';
        $path = 'clientes/cadastrar';
        $columns = ['#', 'CNPJ', 'Razão Social', 'Nome Fantasia', 'Logradouro', 'Complemento', 'Estado', 'Cidade', 'Bairro', 'CEP', 'Tempo de Transporte'];
        $indexes = ['id', 'cnpj', 'razao_social', 'nome_fantasia', 'end_logradouro', 'end_complemento', 'estado', 'cidade', 'bairro', 'cep', 'tempo_transp'];
        $infos = $clientes;
    @endphp
@endsection