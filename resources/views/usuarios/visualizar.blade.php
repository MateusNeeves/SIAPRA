@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Usuários';
        $path = 'usuarios';
        $columns = ['#', 'Usuário', 'Nome'];
        $indexes = ['id', 'username', 'name'];
        $editables = ['username', 'name'];

        $infos = $usuarios;
    @endphp
@endsection

@section('content')
    <!-- Username -->
    <div>
        <x-input-label :value="__('Username *')" />
        <x-text-input id="username" class="input block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus/>
    </div>

    <!-- Nome -->
    <div class="mt-4">
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="name" class="input block mt-1 w-full" type="text" name="name" :value="old('name')" required/>
    </div>

    <!-- Senha -->
    <div class="mt-4">
        <x-input-label :value="__('Senha *')" />
        <x-text-input id="password" class="input block mt-1 w-full" type="password" name="password" required/>
    </div>
@endsection