@extends('layouts.cadastrar')

@section('title', 'Cadastrar Produto')

@section('path', 'cadastrar')

@section('content')
    <!-- Nome -->
    <div>
        <x-input-label :value="__('Nome *')" />
        <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus/>
        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
    </div>

    <!-- Descricao -->
    <div class="mt-4">
        <x-input-label :value="__('Descricao *')" />
        <x-text-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao')" required autofocus/>
        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
    </div>

    <!-- Tipo do Produto -->
    <div class="mt-4">
        <x-input-label :value="__('Tipo do Produto *')" />
        <select id="id_tipo" class="block mt-1 w-full border rounded" name="id_tipo" :value="old('id_tipo')" required autofocus>
            <option value="" hidden></option>
            @foreach ($tipos as $tipo)
                <option value="{{$tipo['id']}}"> {{$tipo['nome']}} </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('id_tipo')" class="mt-2" />
    </div>

    <!-- Quantidade Aceitável -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Aceitável *')" />
        <x-text-input id="qtd_aceitavel" class="block mt-1 w-full" type="number" name="qtd_aceitavel" :value="old('qtd_aceitavel')" required/>
        <x-input-error :messages="$errors->get('qtd_aceitavel')" class="mt-2" />
    </div>

    <!-- Quantidade Mínima -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade Mínima *')" />
        <x-text-input id="qtd_minima" class="block mt-1 w-full" type="number" name="qtd_minima" :value="old('qtd_minima')" required/>
        <x-input-error :messages="$errors->get('qtd_minima')" class="mt-2" />
    </div>
@endsection