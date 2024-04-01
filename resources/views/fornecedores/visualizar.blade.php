@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Fornecedores';
        $path = 'fornecedores';
        $columns = ['#', 'Nome', 'Endereço', 'País', 'Contato', 'Telefone', 'Email', 'Site', 'CNPJ'];
        $indexes = ['id', 'nome', 'endereco', 'pais', 'nome_contato', 'telefone', 'email', 'site', 'cnpj'];
        $infos = $fornecedores;
    @endphp
@endsection