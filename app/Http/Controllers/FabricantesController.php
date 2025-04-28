<?php

namespace App\Http\Controllers;

use App\Models\Acao;
use App\Models\Pais;
use App\Models\Fabricante;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FabricantesController extends Controller
{
    public function index(){
        $fabricantes = Fabricante::all();
        foreach ($fabricantes as $i => $fab) {
            $fabricantes[$i]->telefone = formatPhone($fabricantes[$i]->telefone);

            if ($fab->pais == "BRASIL"){
                $fabricantes[$i]->cep = formatCep($fabricantes[$i]->cep);
                $fabricantes[$i]->cnpj = formatCnpj($fabricantes[$i]->cnpj);
                
                $fabricantes[$i]->endereco = $fab->endereco . ", " . $fab->numero . ($fab->complemento != null ? ", " . $fab->complemento : "") . ", " . $fab->cidade . " - " . $fab->estado . ", " . $fab->cep;
            }
        }
        $paises = Pais::all();
        return view('fabricantes/visualizar', ['fabricantes' => $fabricantes, 'paises' => $paises]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES

        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => 'unique:fabricantes',
            'cnpj' => 'unique:fabricantes'],
            
            ['nome.unique' => 'Já existe um Fabricante com esse Nome',
            'cnpj.unique' => 'Já existe um Fabricante com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
        
        try{
            DB::beginTransaction();
            
            // SALVANDO FABRICANTE
            $fabricante = new Fabricante();
            
            $fabricante->nome = $request->nome;
            $fabricante->pais = $request->pais;
            $fabricante->endereco = mb_strtoupper($request->endereco);
            $fabricante->nome_contato = $request->nome_contato;
            $fabricante->telefone = $request->telefone;
            $fabricante->email = mb_strtolower($request->email);
            $fabricante->site = $request->site;
            $fabricante->linha_fornecimento = $request->linha_fornecimento;

            if ($request->pais == "BRASIL"){
                $fabricante->cnpj = $request->cnpj;
                $fabricante->cep = $request->cep;
                $fabricante->numero = $request->numero;
                $fabricante->complemento = mb_strtoupper($request->complemento);
                $fabricante->cidade = mb_strtoupper($request->cidade);
                $fabricante->estado = mb_strtoupper($request->estado);
            }

            $fabricante->save();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Fabricante')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Fabricante adicionado:\n" .
                "- ID do Fabricante: {$fabricante->id}\n" .
                "- Nome: {$fabricante->nome}\n" .
                "- País: {$fabricante->pais}\n" .
                "- CNPJ: " . ($fabricante->cnpj ?: '(não informado)') . "\n" .
                "- CEP: " . ($fabricante->cep ?: '(não informado)') . "\n" .
                "- Endereço: {$fabricante->endereco}\n" .
                "- Número: " . ($fabricante->numero ?: '(não informado)') . "\n" .
                "- Complemento: " . ($fabricante->complemento ?: '(não informado)') . "\n" .
                "- Cidade: " . ($fabricante->cidade ?: '(não informado)') . "\n" .
                "- Estado: " . ($fabricante->estado ?: '(não informado)') . "\n" .
                "- Nome do Contato: " . ($fabricante->nome_contato ?: '(não informado)') . "\n" .
                "- Telefone: {$fabricante->telefone}\n" .
                "- Email: " . ($fabricante->email ?: '(não informado)') . "\n" .
                "- Site: " . ($fabricante->site ?: '(não informado)') . "\n" .
                "- Linha de Fornecimento: " . ($fabricante->linha_fornecimento ?: '(não informado)') . "\n";

            $log->save();
 
            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Fabricante cadastrado com sucesso');

        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function edit(Request $request){
        $fabricante = Fabricante::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['fabricante' => $fabricante, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => Rule::unique('fabricantes')->ignore($request->id_edit),
            'cnpj' => Rule::unique('fabricantes')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Fabricante com esse Nome',
            'cnpj.unique' => 'Já existe um Fabricante com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();   
        
        try{
            DB::beginTransaction();

            $fabricante = Fabricante::findOrFail($request->id_edit);

            $fabricanteAntes = $fabricante->toArray();

            // SALVANDO FABRICANTE
            if ($request->pais == "BRASIL"){
                $fabricante->update([
                    'nome' => $request->nome,
                    'pais' => $request->pais,
                    'cnpj' => $request->cnpj,
                    'cep' => $request->cep,
                    'endereco' => mb_strtoupper($request->endereco),
                    'numero' => $request->numero,
                    'complemento' => mb_strtoupper($request->complemento),
                    'cidade' => mb_strtoupper($request->cidade),
                    'estado' => mb_strtoupper($request->estado),
                    'nome_contato' => $request->nome_contato,
                    'telefone' => $request->telefone,
                    'email' => mb_strtolower($request->email),
                    'site' => $request->site,
                    'linha_fornecimento' => $request->linha_fornecimento,
                ]);
            }
            else{
                $fabricante->update([
                    'nome' => $request->nome,
                    'pais' => $request->pais,
                    'cnpj' => null,
                    'cep' => null,
                    'endereco' => mb_strtoupper($request->endereco),
                    'numero' => null,
                    'complemento' => null,
                    'cidade' => null,
                    'estado' => null,
                    'nome_contato' => $request->nome_contato,
                    'telefone' => $request->telefone,
                    'email' => mb_strtolower($request->email),
                    'site' => $request->site,
                    'linha_fornecimento' => $request->linha_fornecimento,
                ]);
            }

            $fabricanteDepois = $fabricante->refresh()->toArray();

            if (array_diff_assoc($fabricanteAntes, $fabricanteDepois) != []){
                $log = new Log();
    
                $log->id_user = Auth::user()->id;
                $log->id_acao = Acao::where('descricao', 'Editar Fabricante')->first()["id"];
                $log->tipo = "Info";
                $log->data_hora = now();
                $log->descricao = 
                    "Fabricante editado:\n" .
                    "- ID do Fabricante: {$fabricanteAntes['id']}\n" .
                    "- Nome do Fabricante: {$fabricanteAntes['nome']}\n\n" .
                    "Campos alterados:\n";
    
                    foreach ($fabricanteDepois as $campo => $valor) {
                        if ($valor != ($fabricanteAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($fabricanteAntes[$campo] === null || $fabricanteAntes[$campo] === '' ? '(não informado)' : $fabricanteAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }
    
                $log->save();
            }

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Fabricante editado com sucesso');
        }

        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }

    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();
            
            $fabricante = Fabricante::find($request->id_delete);
            $fabricante->delete();

            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Fabricante')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Fabricante deletado:\n" . 
                "- ID do Fabricante: {$fabricante->id}\n" .
                "- Nome: {$fabricante->nome}\n" .
                "- País: {$fabricante->pais}\n" .
                "- CNPJ: " . ($fabricante->cnpj ?: '(não informado)') . "\n" .
                "- CEP: " . ($fabricante->cep ?: '(não informado)') . "\n" .
                "- Endereço: {$fabricante->endereco}\n" .
                "- Número: " . ($fabricante->numero ?: '(não informado)') . "\n" .
                "- Complemento: " . ($fabricante->complemento ?: '(não informado)') . "\n" .
                "- Cidade: " . ($fabricante->cidade ?: '(não informado)') . "\n" .
                "- Estado: " . ($fabricante->estado ?: '(não informado)') . "\n" .
                "- Nome do Contato: " . ($fabricante->nome_contato ?: '(não informado)') . "\n" .
                "- Telefone: {$fabricante->telefone}\n" .
                "- Email: " . ($fabricante->email ?: '(não informado)') . "\n" .
                "- Site: " . ($fabricante->site ?: '(não informado)') . "\n" .
                "- Linha de Fornecimento: " . ($fabricante->linha_fornecimento ?: '(não informado)') . "\n";
            $log->save();

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Fabricante deletado com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para deletar esse Fabricante, pois outras informações dependem dele.')->withInput();
        } 
    }
}
