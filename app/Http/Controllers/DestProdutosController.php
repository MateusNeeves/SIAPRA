<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\Dest_Produto;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        
        try{
            DB::beginTransaction();

            // SALVANDO TIPO DE PRODUTO
            $dest_produto = new Dest_Produto;

            $dest_produto->nome = $request->nome;

            $dest_produto->save();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Destino de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Destino de Produto adicionado:\n" .
                "- Nome: {$dest_produto->nome}\n";

            $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Destino de Produto cadastrado com sucesso');

        }
        catch (\Exception $exception) {
            DB::rollback();

            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function edit(Request $request){
        if ($request->id_edit == 1){
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para editar esse Destino de Produto.');
        }

        $dest_produto = Dest_Produto::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['dest_produto' => $dest_produto, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['nome' => $request->nome],
            
            ['nome' => Rule::unique('tipos_produtos')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Destino de Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     
        
        try{
            DB::beginTransaction();

            $dest_produto = Dest_Produto::findOrFail($request->id_edit);

            $dest_produtoAntes = $dest_produto->toArray();

            // SALVANDO TIPO DE PRODUTO
            $dest_produto->update([
                'nome' => $request->nome
            ]);

            $dest_produtoDepois = $dest_produto->refresh()->toArray();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Editar Destino de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Destino de Produto editado:\n" .
                "- ID do Destino de Produto: {$dest_produtoAntes['id']}\n" .
                "- Nome do Destino de Produto: {$dest_produtoAntes['nome']}\n\n" .
                "Campos alterados:\n";

                foreach ($dest_produtoDepois as $campo => $valor) {
                    if ($valor != ($tipo_produtoAntes[$campo] ?? null)) {
                        $log->descricao .= "- {$campo}: " .
                            ($dest_produtoAntes[$campo] === null || $dest_produtoAntes[$campo] === '' ? '(não informado)' : $dest_produtoAntes[$campo]) . 
                            " -> " . 
                            ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                    }
                }

            $log->save();

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Destino do Produto editado com sucesso');
        }

        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function destroy(Request $request){
        $dest_produto = Dest_Produto::find($request->id_delete);
        $dest_produtoAntes = $dest_produto->toArray();

        try{
            DB::beginTransaction();
            
            $dest_produto->delete();

            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Destino de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Destino de Produto deletado:\n" . 
                "- Nome: {$dest_produto->nome}\n";

            $log->save();

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Destino de Produto deletado com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
        
            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Tentativa de Deletar Destino de Produto')->first()["id"];
            $log->tipo = "Erro";
            $log->data_hora = now();
            $log->descricao = 
                "Tentativa falha de deletar destino de produto:\n" . 
                "- ID do Tipo de Produto: {$dest_produtoAntes['id']}\n" . 
                "- Nome do Fabricante: {$dest_produtoAntes['nome']}\n" . 
                "- Erro: {$exception->getMessage()}";
            $log->save();

            return redirect()->back()->with('alert-danger', 'Você não tem permissão para deletar esse Destino de Produto, pois outras informações dependem dele.')->withInput();
        } 
    }
}
