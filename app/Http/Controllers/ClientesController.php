<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(){
        $clientes = Cliente::all();
        return view('clientes/visualizar', ['clientes' => $clientes]);
    }

    public function register(){
        return view('clientes/cadastrar');
    }

    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),

            ['cnpj' => 'unique:clientes',
            'razao_social' => 'unique:clientes',
            'nome_fantasia' => 'unique:clientes'],

            ['cnpj.unique' => 'Cliente com esse CNPJ já existe',
            'razao_social.unique' => 'Cliente com essa Razão Social já existe',
            'nome_fantasia.unique' => 'Cliente com esse Nome Fantasia já existe']
        );

        if ($validator->fails()){
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput(); 
        }

        $cliente = new Cliente;

        $cliente->cnpj = $request->cnpj;
        $cliente->razao_social = $request->razao_social;
        $cliente->nome_fantasia = $request->nome_fantasia;
        $cliente->end_logradouro = $request->end_logradouro;
        $cliente->end_complemento = $request->end_complemento;
        $cliente->estado = $request->estado;
        $cliente->cidade = $request->cidade;
        $cliente->bairro = $request->bairro;
        $cliente->cep = $request->cep;
        $cliente->tempo_transp = $request->tempo_transp;

        $cliente->save();

        return redirect('clientes')->with('alert-success', 'Cliente cadastrado com sucesso');
    }
}
