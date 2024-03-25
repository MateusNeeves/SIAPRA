<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutosController extends Controller
{
    public function index(){
        $produtos = collect(DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.DATA_EMISSAO, P.QTD_ACEITAVEL, P.QTD_MINIMA
                                        FROM PRODUTOS P 
                                        INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID)'));
        $produtos = json_decode(json_encode($produtos), true);

        return view('produtos/visualizar', ['produtos' => $produtos]);
    }

    public function register(){
        $tipos = Tipo_Produto::all();
        return view('produtos/cadastrar', ['tipos' => $tipos]);
    }

    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['nome' => 'unique:produtos'],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     
    
        $produto = new Produto;

        $produto->nome = $request->nome;
        $produto->descricao = $request->descricao;
        $produto->id_tipo = $request->id_tipo;
        $produto->data_emissao = now();
        $produto->qtd_aceitavel = $request->qtd_aceitavel;
        $produto->qtd_minima = $request->qtd_minima;

        $produto->save();

        return redirect('produtos')->with('alert-success', 'Produto cadastrado com sucesso');
    }
}
