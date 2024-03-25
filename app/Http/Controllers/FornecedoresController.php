<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FornecedoresController extends Controller
{
    public function index(){
        $fornecedores = Fornecedor::all();
        return view('fornecedores/visualizar', ['fornecedores' => $fornecedores]);
    }

    public function register(){
        $paises = Pais::all();
        return view('fornecedores/cadastrar', ['paises' => $paises]);
    }

    public function store(Request $request){
        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => 'unique:fornecedores',
            'cnpj' => 'unique:fornecedores'],
            
            ['nome.unique' => 'Já existe um Fornecedor com esse Nome',
            'cnpj.unique' => 'Já existe um Fornecedor com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     

        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fornecedor do Brasil deve ter um CNPJ')->withInput();     
    
        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fornecedor extrangeiro não deve possuir CNPJ')->withInput();     
    
        $fornecedor = new Fornecedor();
        
        $fornecedor->nome = $request->nome;
        $fornecedor->endereco = $request->endereco;
        $fornecedor->pais = $request->pais;
        $fornecedor->nome_contato = $request->nome_contato;
        $fornecedor->telefone = $request->telefone;
        $fornecedor->email = $request->email;
        $fornecedor->site = $request->site;
        $fornecedor->cnpj = $request->cnpj;

        $fornecedor->save();
        return redirect('fornecedores')->with('alert-success', 'Fornecedor cadastrado com sucesso');

    }
}
