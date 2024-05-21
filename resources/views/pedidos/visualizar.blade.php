@extends('layouts.visualizar')

@section('variables')
    @php
        $title = ['Pedidos', 'Pedido'];
        $path = 'pedidos';
        $columns = ['#', 'Cliente', 'Usuário', 'Qtd Doses', 'Data Solicitação', 'Data Entrega'];
        $indexes = ['id', 'nome_fantasia', 'username', 'qtd_doses', 'data_solicitacao', 'data_entrega'];
        $infos = $pedidos;
    @endphp
@endsection


@section('content')
    @php
        $pedido = Session::get('pedido') ?? null;
    @endphp
    <!-- Cliente -->
    <div>
        <x-input-label :value="__('Cliente *')" />
        <select id="cliente" class="block mt-1 w-full border rounded" name="cliente" required>
            <option value="" hidden></option>
            @foreach ($clientes as $cliente)
                <option value="{{$cliente['nome_fantasia']}}" {{$cliente['nome_fantasia'] == (old('cliente') ?? $pedido->cliente ?? "") ? "selected" : ""}}> {{$cliente['nome_fantasia']}} </option>
                
            @endforeach
        </select>
    </div>

    <!-- Qtd Doses -->
    <div class="mt-4">
        <x-input-label :value="__('Quantidade de Doses *')" />
        <x-text-input id="qtd_doses" class="block mt-1 w-full" type="number" name="qtd_doses" :value="old('qtd_doses', $pedido->qtd_doses ?? '')" required/>
    </div>
    
    <!-- Data Solicitação -->
    <div class="mt-4">
        <x-input-label :value="__('Data Solicitação *')" />
        <x-text-input id="data_solicitacao" class="block mt-1 w-full" type="datetime-local" name="data_solicitacao" :value="old('data_solicitacao', $pedido->data_solicitacao ?? '')" required/>
    </div>

    <!-- Data Entrega -->
    <div class="mt-4">
        <x-input-label :value="__('Data Entrega *')" />
        <x-text-input id="data_entrega" class="block mt-1 w-full" type="date" name="data_entrega" :value="old('data_entrega', $pedido->data_entrega ?? '')" required/>
    </div>
@endsection