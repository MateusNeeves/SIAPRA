<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutosController extends Controller
{
    public function index(){
        $produtos = collect(DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA
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
        $produto->qtd_aceitavel = $request->qtd_aceitavel;
        $produto->qtd_minima = $request->qtd_minima;

        $produto->save();

        return redirect()->route('produtos')->with('alert-success', 'Produto cadastrado com sucesso');
    }

    public function edit($id){
        $produto = Produto::find($id);
        $tipos = Tipo_Produto::all();
        if ($produto)
            return view('produtos/editar', ['produto' => $produto, 'tipos' => $tipos]);
        else
            return redirect()->route('produtos')->with('alert-danger', 'Produto de id #' . $id . ' não encontrado.');
    }

    public function update(Request $request, $id){
        $validator = Validator::make(
            $request->all(),
            
            ['nome' => Rule::unique('produtos')->ignore($id)],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput(); 

        Produto::findOrFail($id)->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'id_tipo' => $request->id_tipo,
            'qtd_aceitavel' => $request->qtd_aceitavel,
            'qtd_minima' => $request->qtd_minima
        ]);
        return redirect()->route('produtos')->with('alert-success', 'Produto editado com sucesso');
    }

    public function destroy(Request $request){
        try{
            Produto::findOrFail($request->id)->delete();
    
            return redirect()->route('produtos')->with('alert-success', 'Produtos exclúido com sucesso');
        }
        catch (\Exception $exception) {
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro ao deletar o produto: ' . $exception->getMessage())->withInput(); 
        }
    }
}
