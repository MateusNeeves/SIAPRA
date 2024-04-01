@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Fabricantes';
        $path = 'fabricantes';
        $columns = ['#', 'Nome', 'Endereço', 'País', 'Contato', 'Telefone', 'Email', 'Site', 'CNPJ'];
        $indexes = ['id', 'nome', 'endereco', 'pais', 'nome_contato', 'telefone', 'email', 'site', 'cnpj'];
        $infos = $fabricantes;
    @endphp
@endsection