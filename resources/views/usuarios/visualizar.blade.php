@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Usuários', 'Usuário'];
        $path = 'usuarios';
        $columns = ['#', 'Usuário', 'Nome', 'CPF', 'Email', 'Telefone', 'Classes'];
        $indexes = ['id', 'username', 'name', 'cpf', 'email', 'phone', 'classes'];
        $infos = $usuarios;
    @endphp
@endsection

@section('content')
    @php
        $usuario = Session::get('usuario') ?? null;
        $classes = Session::get('classes') ?? [];
        $classesSelected = Session::get('classesSelected') ?? null;

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

    <!-- CPF -->
    <div class="mt-4">
        <div class="flex justify-between">
            <x-input-label :value="__('CPF *')" />
            <x-input-label :value="__('(Apenas Números)')" />
        </div>
        <x-text-input id="cpf" class="block mt-1 w-full" type="text" maxlength="11" name="cpf" :value="old('name', $usuario->cpf ?? '')" required/>
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label :value="__('Email *')" />
        <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('name', $usuario->email ?? '')" required/>
    </div>

    <!-- Telefone -->
    <div class="mt-4">
        <div class="flex justify-between">
            <x-input-label :value="__('Telefone *')" />
            <x-input-label :value="__('(Apenas Números)')" />
        </div>
        <x-text-input id="phone" class="block mt-1 w-full" type="text" maxlength="11" name="phone" :value="old('name', $usuario->phone ?? '')" required/>
    </div>

    <!-- Senha -->
    <div class="mt-4">
        <x-input-label :value="__('Senha *')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required/>
    </div>

    <!-- Classes -->
    <div class="mt-4">
        <div class="flex justify-between mb-1">
            <x-input-label :value="__('Classes *')" />
            <x-input-label :value="__('Selecione uma ou mais opções')" />
        </div>
        <select class="multiple-select block mt-1 w-full border rounded" name="classes[]" multiple required>
            @foreach ($classes as $classe)
                <option value="{{$classe['nome']}}" {{in_array($classe['nome'], $classesSelected ?? old('classes') ?? []) ? "selected" : ""}} > {{$classe['nome']}} </option>
            @endforeach
        </select>
    </div>
@endsection