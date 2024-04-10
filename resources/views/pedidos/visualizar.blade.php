@extends('layouts.visualizar')

@section('variables')
    @php
        $title = 'Pedidos';
        $path = 'pedidos';
        $columns = ['#', 'Cliente', 'Usuário', 'Qtd Doses', 'Data Solicitação', 'Data Entrega'];
        $indexes = ['id', 'nome_fantasia', 'username', 'qtd_doses', 'data_solicitacao', 'data_entrega'];
        $editables = ['nome_fantasia', 'qtd_doses', 'data_solicitacao', 'data_entrega'];
        $infos = $pedidos;
    @endphp
@endsection


@section('content')
    <!-- Cliente -->
    <div>
        <x-input-label :value="__('Cliente *')" />
        <select id="cliente" class="input select block mt-1 w-full border rounded" name="cliente" required autofocus>
            <option value="" hidden></option>
            @foreach ($clientes as $cliente)
                <option value="{{$cliente['nome_fantasia']}}" {{(old('cliente') == $cliente['nome_fantasia']) ? "selected" : "" }}> {{$cliente['nome_fantasia']}} </option>
                
            @endforeach
        </select>
    </div>

    <!-- Qtd Doses -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade de Doses *')" />
        <x-text-input id="qtd_doses" class="input block mt-1 w-full" type="number" name="qtd_doses" :value="old('qtd_doses')" required autofocus/>
    </div>
    
    <!-- Data Solicitação -->
    <div class="mt-4">
        <x-input-label :value="__('Data Solicitação *')" />
        <x-text-input id="data_solicitacao" class="input block mt-1 w-full" type="datetime-local" name="data_solicitacao" :value="old('data_solicitacao')" required/>
    </div>

    <!-- Data Entrega -->
    <div class="mt-4">
        <x-input-label :value="__('Data Entrega *')" />
        <x-text-input id="data_entrega" class="input block mt-1 w-full" type="date" name="data_entrega" :value="old('data_entrega')" required/>
    </div>
@endsection