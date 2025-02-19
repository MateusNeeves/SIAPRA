<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TiposProdutosController extends Controller
{
    public function index(){
        $tipos_produtos = Tipo_Produto::all();
        return view('tipos_produtos/visualizar', ['tipos_produtos' => $tipos_produtos]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES

        $validator = Validator::make(
            ['nome' => $request->nome,
            'sigla' => mb_strtoupper($request->sigla)],
            
            ['nome' => 'unique:tipos_produtos',
            'sigla' => 'unique:tipos_produtos'],
            
            ['nome.unique' => 'Já existe um Tipo com esse Nome',
            'sigla.unique' => 'Já existe um Tipo com essa Sigla']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
        
        try{
            DB::beginTransaction();

            // SALVANDO TIPO DE PRODUTO
            $tipo_produto = new Tipo_Produto;

            $tipo_produto->nome = $request->nome;
            $tipo_produto->descricao = $request->descricao;
            $tipo_produto->sigla = mb_strtoupper($request->sigla);

            $tipo_produto->save();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Tipo de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Tipo de Produto adicionado:\n" .
                "- ID do Tipo de Produto: {$tipo_produto->id}\n" .
                "- Nome: {$tipo_produto->nome}\n" .
                "- Descrição: " . ($tipo_produto->descricao ?: '(não informado)') . "\n" . 
                "- Sigla: {$tipo_produto->sigla}\n";

            $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Tipo de Produto cadastrado com sucesso');

        }
        catch (\Exception $exception) {
            DB::rollback();

            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function edit(Request $request){
        $tipo_produto = Tipo_Produto::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['tipo_produto' => $tipo_produto, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['nome' => $request->nome,
            'sigla' => strtoupper($request->sigla)],
            
            ['nome' => Rule::unique('tipos_produtos')->ignore($request->id_edit),
            'sigla' => Rule::unique('tipos_produtos')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Tipo com esse Nome',
            'sigla.unique' => 'Já existe um Tipo com essa Sigla']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     
        
        try{
            DB::beginTransaction();

            $tipo_produto = Tipo_Produto::findOrFail($request->id_edit);

            $tipo_produtoAntes = $tipo_produto->toArray();

            // SALVANDO TIPO DE PRODUTO
            $tipo_produto->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'sigla' => mb_strtoupper($request->sigla),
            ]);

            $tipo_produtoDepois = $tipo_produto->refresh()->toArray();

            if (array_diff_assoc($tipo_produtoAntes, $tipo_produtoDepois) != []){

                $log = new Log();

                $log->id_user = Auth::user()->id;
                $log->id_acao = Acao::where('descricao', 'Editar Tipo de Produto')->first()["id"];
                $log->tipo = "Info";
                $log->data_hora = now();
                $log->descricao = 
                    "Tipo de Produto editado:\n" .
                    "- ID do Tipo de Produto: {$tipo_produtoAntes['id']}\n" .
                    "- Nome do Tipo de Produto: {$tipo_produtoAntes['nome']}\n\n" .
                    "Campos alterados:\n";

                    foreach ($tipo_produtoDepois as $campo => $valor) {
                        if ($valor != ($tipo_produtoAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($tipo_produtoAntes[$campo] === null || $tipo_produtoAntes[$campo] === '' ? '(não informado)' : $tipo_produtoAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }

                $log->save();
            }

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Tipo de Produto editado com sucesso');
        }

        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();
            
            $tipo_produto = Tipo_Produto::find($request->id_delete);
            $tipo_produto->delete();

            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Tipo de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Tipo de Produto deletado:\n" . 
                "- ID do Tipo de Produto: {$tipo_produto->id}\n" .
                "- Nome: {$tipo_produto->nome}\n" .
                "- Descrição: " . ($tipo_produto->descricao ?: '(não informado)') . "\n" .
                "- Sigla: {$tipo_produto->sigla}\n";

            $log->save();

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Tipo de Produto deletado com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para deletar esse Tipo de Produto, pois outras informações dependem dele.')->withInput();
        } 
    }
}
