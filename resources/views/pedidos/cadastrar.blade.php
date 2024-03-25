@extends('layouts.cadastrar')

@section('title', 'Cadastrar Pedido')

@section('path', 'cadastrar')

@section('content')
    <!-- Cliente -->
    <div>
        <x-input-label :value="__('Cliente *')" />
        <select id="id_cliente" class="block mt-1 w-full border rounded" name="id_cliente" required autofocus>
            <option value="" hidden></option>
            @foreach ($clientes as $cliente)
                <option value="{{$cliente['id']}}" {{old('id_cliente') == $cliente['id'] ? "selected" : "" }}> {{$cliente['nome_fantasia']}} </option>
            @endforeach
        </select>
    </div>

    <!-- Qtd Doses -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade de Doses *')" />
        <x-text-input id="qtd_doses" class="block mt-1 w-full" type="number" name="qtd_doses" :value="old('qtd_doses')" required autofocus/>
    </div>

    <!-- Data Solicitação -->
    <div class="mt-4">
        <x-input-label :value="__('Data Solicitação *')" />
        <x-text-input id="data_solicitacao" class="block mt-1 w-full" type="datetime-local" name="data_solicitacao" :value="old('data_solicitacao')" required/>
    </div>

    <!-- Data Entrega -->
    <div class="mt-4">
        <x-input-label :value="__('Data Entrega *')" />
        <x-text-input id="data_entrega" class="block mt-1 w-full" type="date" name="data_entrega" :value="old('data_entrega')" required/>
    </div>
@endsection