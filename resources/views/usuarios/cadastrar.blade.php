@extends('layouts.cadastrar')

@section('title', 'Cadastrar Usuário')

@section('path', 'cadastrar')

@section('content')
    <!-- Username -->
    <div>
        <x-input-label :value="__('Username *')" />
        <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus/>
        <x-input-error :messages="$errors->get('username')" class="mt-2" />
    </div>

    <!-- Nome -->
    <div class="mt-4">
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required/>
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Senha -->
    <div class="mt-4">
        <x-input-label :value="__('Senha *')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required/>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
@endsection