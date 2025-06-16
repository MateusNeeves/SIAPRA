<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use Illuminate\Http\Request;
use App\Models\Unidade_Medida;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UnidadesMedidaController extends Controller
{
    public function index(){
        $unidades_medida = Unidade_Medida::all();
        return view('unidades_medida/visualizar', ['unidades_medida' => $unidades_medida]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES

        $validator = Validator::make(
            ['nome' => $request->nome,
            'sigla' => $request->sigla],
            
            ['nome' => 'unique:unidade_medida',
            'sigla' => 'unique:unidade_medida'],
            
            ['nome.unique' => 'Já existe uma Unidade de Medida com esse Nome',
            'sigla.unique' => 'Já existe uma Unidade de Medida com essa Sigla']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
        
        try{
            DB::beginTransaction();

            // SALVANDO TIPO DE PRODUTO
            $unidade_medida = new Unidade_Medida();

            $unidade_medida->nome = $request->nome;
            $unidade_medida->sigla = $request->sigla;

            $unidade_medida->save();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Unidade de Medida')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Unidade de Medida adicionada:\n" .
                "- ID da Unidade de Medida: {$unidade_medida->id}\n" .
                "- Nome: {$unidade_medida->nome}\n" .
                "- Sigla: {$unidade_medida->sigla}\n";

            $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Unidade de Medida cadastrada com sucesso');

        }
        catch (\Exception $exception) {
            DB::rollback();

            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function edit(Request $request){
        $unidade_medida = Unidade_Medida::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['unidade_medida' => $unidade_medida, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['nome' => $request->nome,
            'sigla' => $request->sigla],
            
            ['nome' => Rule::unique('unidade_medida')->ignore($request->id_edit),
            'sigla' => Rule::unique('unidade_medida')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe uma Unidade de Medida com esse Nome',
            'sigla.unique' => 'Já existe uma Unidade de Medida com essa Sigla']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     
        
        try{
            DB::beginTransaction();

            $unidade_medida = Unidade_Medida::findOrFail($request->id_edit);

            $unidade_medidaAntes = $unidade_medida->toArray();

            // SALVANDO TIPO DE PRODUTO
            $unidade_medida->update([
                'nome' => $request->nome,
                'sigla' => $request->sigla,
            ]);

            $unidade_medidaDepois = $unidade_medida->refresh()->toArray();

            if (array_diff_assoc($unidade_medidaAntes, $unidade_medidaDepois) != []){

                $log = new Log();

                $log->id_user = Auth::user()->id;
                $log->id_acao = Acao::where('descricao', 'Editar Unidade de Medida')->first()["id"];
                $log->tipo = "Info";
                $log->data_hora = now();
                $log->descricao = 
                    "Unidade de Medida editada:\n" .
                    "- ID da Unidade de Medida: {$unidade_medidaAntes['id']}\n" .
                    "- Nome do Tipo de Produto: {$unidade_medidaAntes['nome']}\n\n" .
                    "Campos alterados:\n";

                    foreach ($unidade_medidaDepois as $campo => $valor) {
                        if ($valor != ($unidade_medidaAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($unidade_medidaAntes[$campo] === null || $unidade_medidaAntes[$campo] === '' ? '(não informado)' : $unidade_medidaAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }

                $log->save();
            }

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Unidade de Medida editada com sucesso');
        }

        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();
            
            $unidade_medida = Unidade_Medida::find($request->id_delete);
            $unidade_medida->delete();

            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Unidade de Medida')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Unidade de Medida deletada:\n" . 
                "- ID da Unidade de Medida: {$unidade_medida->id}\n" .
                "- Nome: {$unidade_medida->nome}\n" .
                "- Sigla: {$unidade_medida->sigla}\n";

            $log->save();

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Unidade de Medida deletada com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para deletar essa Unidade de Medida, pois outras informações dependem dela.')->withInput();
        } 
    }
}
