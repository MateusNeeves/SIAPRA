@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Usuários';
        $path = 'usuarios';
        $columns = ['#', 'Usuário', 'Nome'];
        $indexes = ['id', 'username', 'name'];
        $infos = $usuarios;
    @endphp
@endsection