<?php

namespace App\Http\Controllers;

use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
    
        $tipo_produto = new Tipo_Produto;

        $tipo_produto->nome = $request->nome;
        $tipo_produto->descricao = $request->descricao;
        $tipo_produto->sigla = strtoupper($request->sigla);

        $tipo_produto->save();

        return redirect()->route('tipos_produtos')->with('alert-success', 'Tipo de Produto cadastrado com sucesso');
    }

    public function edit($id){
        $tipo_produto = Tipo_Produto::find($id);
        if ($tipo_produto)
            return view('tipos_produtos/editar', ['tipo_produto' => $tipo_produto]);
        else
            return redirect()->route('tipos_produtos')->with('alert-danger', 'Tipo de Produto de id #' . $id . ' não encontrado.');
    }

    public function update(Request $request){
        $validator = Validator::make(
            ['nome' => $request->nome,
            'sigla' => strtoupper($request->sigla)],
            
            ['nome' => Rule::unique('tipos_produtos')->ignore($request->id),
            'sigla' => Rule::unique('tipos_produtos')->ignore($request->id)],
            
            ['nome.unique' => 'Já existe um Tipo com esse Nome',
            'sigla.unique' => 'Já existe um Tipo com essa Sigla']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     

            Tipo_Produto::findOrFail($request->id)->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'sigla' => $request->sigla,
        ]);
        return redirect()->route('tipos_produtos')->with('alert-success', 'Tipo de Produto editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        Tipo_Produto::find($request->id)->delete();

        if ($request->soft == 'false'){
            try{
                Tipo_Produto::withTrashed()->find($request->id)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Tipo de Produto excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Tipo de Produto, pois outras informações dependem dele. <br><br> Deseja Desativar esse Tipo de Produto ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Tipo de Produto desativado com sucesso');
    }
}
