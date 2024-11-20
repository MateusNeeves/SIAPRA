<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\Pais;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FornecedoresController extends Controller
{
    public function index(){
        $fornecedores = Fornecedor::all();
        foreach ($fornecedores as $i => $forn) {
            $fornecedores[$i]->telefone = formatPhone($fornecedores[$i]->telefone);
            if ($forn->pais == "BRASIL"){
                $fornecedores[$i]->cep = formatCep($fornecedores[$i]->cep);
                $fornecedores[$i]->cnpj = formatCnpj($fornecedores[$i]->cnpj);

                $fornecedores[$i]->endereco = $forn->endereco . ", " . $forn->numero . ($forn->complemento != null ? ", " . $forn->complemento : "") . ", " . $forn->cidade . " - " . $forn->estado . ", " . $forn->cep;
            }
        }
        $paises = Pais::all();
        return view('fornecedores/visualizar', ['fornecedores' => $fornecedores, 'paises' => $paises]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES

        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => 'unique:fornecedores',
            'cnpj' => 'unique:fornecedores'],
            
            ['nome.unique' => 'Já existe um Fornecedor com esse Nome',
            'cnpj.unique' => 'Já existe um Fornecedor com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();       
        
        try{
            DB::beginTransaction();
            
            $fornecedor = new Fornecedor();
        
            $fornecedor->nome = $request->nome;
            $fornecedor->pais = $request->pais;
            $fornecedor->endereco = mb_strtoupper($request->endereco);
            $fornecedor->nome_contato = $request->nome_contato;
            $fornecedor->telefone = $request->telefone;
            $fornecedor->email = mb_strtolower($request->email);
            $fornecedor->site = $request->site;
    
            if ($request->pais == "BRASIL"){
                $fornecedor->cnpj = $request->cnpj;
                $fornecedor->cep = $request->cep;
                $fornecedor->numero = $request->numero;
                $fornecedor->complemento = mb_strtoupper($request->complemento);
                $fornecedor->cidade = mb_strtoupper($request->cidade);
                $fornecedor->estado = mb_strtoupper($request->estado);
            }
    
            $fornecedor->save();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Fornecedor')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Fornecedor adicionado:\n" .
                "- ID: {$fornecedor->id}\n" .
                "- Nome: {$fornecedor->nome}\n" .
                "- País: {$fornecedor->pais}\n" .
                "- CNPJ: " . ($fornecedor->cnpj ?: '(não informado)') . "\n" .
                "- CEP: " . ($fornecedor->cep ?: '(não informado)') . "\n" .
                "- Endereço: {$fornecedor->endereco}\n" .
                "- Número: " . ($fornecedor->numero ?: '(não informado)') . "\n" .
                "- Complemento: " . ($fornecedor->complemento ?: '(não informado)') . "\n" .
                "- Cidade: " . ($fornecedor->cidade ?: '(não informado)') . "\n" .
                "- Estado: " . ($fornecedor->estado ?: '(não informado)') . "\n" .
                "- Nome do Contato: " . ($fornecedor->nome_contato ?: '(não informado)') . "\n" .
                "- Telefone: {$fornecedor->telefone}\n" .
                "- Email: " . ($fornecedor->email ?: '(não informado)') . "\n" .
                "- Site: " . ($fornecedor->site ?: '(não informado)');

            $log->save();
            
            DB::commit();
            
            return redirect()->route('fornecedores')->with('alert-success', 'Fornecedor cadastrado com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function edit(Request $request){
        $fornecedor = Fornecedor::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['fornecedor' => $fornecedor, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => Rule::unique('fornecedores')->ignore($request->id_edit),
            'cnpj' => Rule::unique('fornecedores')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Fornecedor com esse Nome',
            'cnpj.unique' => 'Já existe um Fornecedor com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput(); 
        
        try{
            DB::beginTransaction();

            $fornecedor = Fornecedor::findOrFail($request->id_edit);

            $fornecedorAntes = $fornecedor->toArray();

            // SALVANDO FABRICANTE
            if ($request->pais == "BRASIL"){
                $fornecedor->update([
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
                ]);
            }
            else{
                $fornecedor->update([
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
                ]);
            }

            $fornecedorDepois = $fornecedor->refresh()->toArray();

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Editar Fornecedor')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Fornecedor editado:\n" .
                "- ID do Fornecedor: {$fornecedorAntes['id']}\n" .
                "- Nome do Fornecedor: {$fornecedorAntes['nome']}\n\n" .
                "Campos alterados:\n";

                foreach ($fornecedorDepois as $campo => $valor) {
                    if ($valor != ($fornecedorAntes[$campo] ?? null)) {
                        $log->descricao .= "- {$campo}: " .
                            ($fornecedorAntes[$campo] === null || $fornecedorAntes[$campo] === '' ? '(não informado)' : $fornecedorAntes[$campo]) . 
                            " -> " . 
                            ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                    }
                }

            $log->save();

            
            DB::commit();
            return redirect()->back()->with('alert-success', 'Fornecedor editado com sucesso');
        }

        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }

    }

    public function destroy(Request $request){
        $fornecedor = Fornecedor::find($request->id_delete);
        $fornecedorAntes = $fornecedor->toArray();

        try{
            DB::beginTransaction();
            
            $fornecedor->delete();

            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Fornecedor')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Fornecedor deletado:\n" . 
                "- ID: {$fornecedor->id}\n" .
                "- Nome: {$fornecedor->nome}\n" .
                "- País: {$fornecedor->pais}\n" .
                "- CNPJ: " . ($fornecedor->cnpj ?: '(não informado)') . "\n" .
                "- CEP: " . ($fornecedor->cep ?: '(não informado)') . "\n" .
                "- Endereço: {$fornecedor->endereco}\n" .
                "- Número: " . ($fornecedor->numero ?: '(não informado)') . "\n" .
                "- Complemento: " . ($fornecedor->complemento ?: '(não informado)') . "\n" .
                "- Cidade: " . ($fornecedor->cidade ?: '(não informado)') . "\n" .
                "- Estado: " . ($fornecedor->estado ?: '(não informado)') . "\n" .
                "- Nome do Contato: " . ($fornecedor->nome_contato ?: '(não informado)') . "\n" .
                "- Telefone: {$fornecedor->telefone}\n" .
                "- Email: " . ($fornecedor->email ?: '(não informado)') . "\n" .
                "- Site: " . ($fornecedor->site ?: '(não informado)');
            $log->save();

            DB::commit();
            
            return redirect()->back()->with('alert-success', 'Fornecedor deletado com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
        
            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Tentativa de Deletar Fornecedor')->first()["id"];
            $log->tipo = "Erro";
            $log->data_hora = now();
            $log->descricao = 
                "Tentativa falha de deletar fornecedor:\n" . 
                "- ID do Fornecedor: {$fornecedorAntes['id']}\n" . 
                "- Nome do Fornecedor: {$fornecedorAntes['nome']}\n" . 
                "- Erro: {$exception->getMessage()}";
            $log->save();

            return redirect()->back()->with('alert-danger', 'Você não tem permissão para deletar esse Fornecedor, pois outras informações dependem dele.')->withInput();
        } 
    }
}
