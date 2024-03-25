<?php

namespace App\Http\Controllers;

use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiposProdutosController extends Controller
{
    public function index(){
        $tipos_produtos = Tipo_Produto::all();
        return view('tipos_produtos/visualizar', ['tipos_produtos' => $tipos_produtos]);
    }

    public function register(){
        return view('tipos_produtos/cadastrar');
    }

    public function store(Request $request){
        $validator = Validator::make(
            ['nome' => $request->nome,
            'sigla' => strtoupper($request->sigla)],
            
            ['nome' => 'unique:tipos_produtos',
            'sigla' => 'unique:tipos_produtos'],
            
            ['nome.unique' => 'Já existe um Tipo com esse Nome',
            'sigla.unique' => 'Já existe um Tipo com essa Sigla']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     
    
        $tipo_produto = new Tipo_Produto;

        $tipo_produto->nome = $request->nome;
        $tipo_produto->descricao = $request->descricao;
        $tipo_produto->sigla = strtoupper($request->sigla);

        $tipo_produto->save();

        return redirect('tipos_produtos')->with('alert-success', 'Tipo de Produto cadastrado com sucesso');
    }
}
