@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Usuários', 'Usuário'];
        $path = 'usuarios';
        $columns = ['#', 'Usuário', 'Nome'];
        $indexes = ['id', 'username', 'name'];
        $infos = $usuarios;
    @endphp
@endsection

@section('content')
    @php
        $usuario = Session::get('usuario') ?? null;
    @endphp
    <!-- Username -->
    <div>
        <x-input-label :value="__('Username *')" />
        <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $usuario->username ?? '')" required/>
    </div>

    <!-- Nome -->
    <div class="mt-4">
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $usuario->name ?? '')" required/>
    </div>

    <!-- Senha -->
    <div class="mt-4">
        <x-input-label :value="__('Senha *')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required/>
    </div>
@endsection