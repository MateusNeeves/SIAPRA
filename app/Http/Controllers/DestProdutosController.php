<?php

namespace App\Http\Controllers;

use App\Models\Dest_Produto;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DestProdutosController extends Controller
{
    public function index(){
        $dest_produtos = Dest_Produto::all();
        return view('dest_produtos/visualizar', ['dest_produtos' => $dest_produtos]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES

        $validator = Validator::make(
            ['nome' => $request->nome],
            
            ['nome' => 'unique:tipos_produtos'],
            
            ['nome.unique' => 'Já existe um Destino de Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
        
        // SALVANDO TIPO DE PRODUTO
        $dest_produto = new Dest_Produto;

        $dest_produto->nome = $request->nome;

        $dest_produto->save();

        return redirect()->route('dest_produtos')->with('alert-success', 'Destino do Produto cadastrado com sucesso');
    }

    public function edit(Request $request){
        $dest_produto = Dest_Produto::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['dest_produto' => $dest_produto, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['nome' => $request->nome],
            
            ['nome' => Rule::unique('tipos_produtos')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Destino do Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     
        
        // SALVANDO TIPO DE PRODUTO
        Dest_Produto::findOrFail($request->id_edit)->update([
            'nome' => $request->nome
        ]);
        return redirect()->route('dest_produtos')->with('alert-success', 'Destino do Produto editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        Dest_Produto::find($request->id_delete)->delete();

        if ($request->soft == 'false'){
            try{
                Dest_Produto::withTrashed()->find($request->id_delete)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Destino do Produto excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Destino do Produto, pois outras informações dependem dele. <br><br> Deseja Desativar esse Destino do Produto ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Destino do Produto desativado com sucesso');
    }
}
