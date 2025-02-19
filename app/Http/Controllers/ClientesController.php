<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(){
        $clientes = Cliente::all();
        return view('clientes/visualizar', ['clientes' => $clientes]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES

        $validator = Validator::make(
            $request->all(),

            ['cnpj' => 'unique:clientes',
            'razao_social' => 'unique:clientes',
            'nome_fantasia' => 'unique:clientes'],

            ['cnpj.unique' => 'Cliente com esse CNPJ já existe',
            'razao_social.unique' => 'Cliente com essa Razão Social já existe',
            'nome_fantasia.unique' => 'Cliente com esse Nome Fantasia já existe']
        );


        if ($validator->fails()){
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput(); 
        }

        try{
            DB::beginTransaction();

            // SALVANDO CLIENTE
            $cliente = new Cliente;
    
            $cliente->cnpj = $request->cnpj;
            $cliente->razao_social = mb_strtoupper($request->razao_social);
            $cliente->nome_fantasia = mb_strtoupper($request->nome_fantasia);
            $cliente->end_logradouro = mb_strtoupper($request->end_logradouro);
            $cliente->end_complemento = mb_strtoupper($request->end_complemento);
            $cliente->estado = mb_strtoupper($request->estado);
            $cliente->cidade = mb_strtoupper($request->cidade);
            $cliente->bairro = mb_strtoupper($request->bairro);
            $cliente->cep = $request->cep;
            $cliente->tempo_transp = $request->tempo_transp;
    
            $cliente->save();

            // ADICIONANDO LOG
            $log = new Log();
            
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Cliente')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Cliente adicionado:\n" .
                "- ID do Cliente: {$cliente->id}\n" .
                "- CNPJ: {$cliente->cnpj}\n" .
                "- Razão Social: {$cliente->razao_social}\n" .
                "- Nome Fantasia: {$cliente->nome_fantasia}\n" .
                "- Endereço: Logradouro: {$cliente->end_logradouro}, Complemento: " . ($cliente->end_complemento ?: '(não informado)') . "\n" .
                "- Estado: {$cliente->estado}\n" .
                "- Cidade: {$cliente->cidade}\n" .
                "- Bairro: {$cliente->bairro}\n" .
                "- CEP: {$cliente->cep}\n" .
                "- Tempo de Transporte: {$cliente->tempo_transp}\n";

            $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Cliente cadastrado com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }


    }

    public function edit(Request $request){
        $cliente = Cliente::where('id', $request->id_edit)->get()[0];

        return redirect()->back()->with(['cliente' => $cliente, 'modal' => '#editModal'])->withInput(); 
    }


    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            $request->all(),
            
            ['cnpj' => Rule::unique('clientes')->ignore($request->id_edit),
            'razao_social' => Rule::unique('clientes')->ignore($request->id_edit),
            'nome_fantasia' => Rule::unique('clientes')->ignore($request->id_edit)],
            
            ['cnpj.unique' => 'Já existe um cliente com esse CNPJ',
            'razao_social.unique' => 'Já existe um Cliente com essa Razão Social',
            'nome_fantasia.unique' => 'Já existe um Cliente com esse Nome Fantasia']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     
        
        try{
            DB::beginTransaction();

            // SALVANDO CLIENTE

            $cliente = Cliente::findOrFail($request->id_edit);
            
            $clienteAntes = $cliente->toArray();

            $cliente->update([
                'cnpj' => $request->cnpj,
                'razao_social' => mb_strtoupper($request->razao_social),
                'nome_fantasia' => mb_strtoupper($request->nome_fantasia),
                'end_logradouro' => mb_strtoupper($request->end_logradouro),
                'end_complemento' => mb_strtoupper($request->end_complemento),
                'estado' => mb_strtoupper($request->estado),
                'cidade' => mb_strtoupper($request->cidade),
                'bairro' => mb_strtoupper($request->bairro),
                'cep' => $request->cep,
                'tempo_transp' => $request->tempo_transp
            ]);

            $clienteDepois = $cliente->refresh()->toArray();

            // ADICIONANDO LOG
            if (array_diff_assoc($clienteAntes, $clienteDepois) != []){
                $log = new Log();

                $log->id_user = Auth::user()->id;
                $log->id_acao = Acao::where('descricao', 'Editar Cliente')->first()["id"];
                $log->tipo = "Info";
                $log->data_hora = now();
                $log->descricao = 
                    "Cliente editado:\n" .
                    "- ID do Cliente: {$clienteAntes['id']}\n" .
                    "- Nome Fantasia: {$clienteAntes['nome_fantasia']}\n\n" .
                    "Campos alterados:\n";

                    foreach ($clienteDepois as $campo => $valor) {
                        if ($valor != ($clienteAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($clienteAntes[$campo] === null || $clienteAntes[$campo] === '' ? '(não informado)' : $clienteAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }

                $log->save();
            }

            DB::commit();
            return redirect()->back()->with('alert-success', 'Cliente editado com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na atualização no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();

            $cliente = Cliente::find($request->id_delete);
            $cliente->delete();

            // ADICIONANDO LOG

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Cliente')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Cliente deletado:\n" .
                "- ID do Cliente: {$cliente->id}\n" .
                "- CNPJ: {$cliente->cnpj}\n" .
                "- Razão Social: {$cliente->razao_social}\n" .
                "- Nome Fantasia: {$cliente->nome_fantasia}\n" .
                "- Endereço: Logradouro: {$cliente->end_logradouro}, Complemento: ($cliente->end_complemento ?: '(não informado)')\n" .
                "- Estado: {$cliente->estado}\n" .
                "- Cidade: {$cliente->cidade}\n" .
                "- Bairro: {$cliente->bairro}\n" .
                "- CEP: {$cliente->cep}\n" .
                "- Tempo de Transporte: {$cliente->tempo_transp}\n";

            $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Cliente excluído com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Cliente, pois outras informações dependem dele.' . $exception->getMessage())->withInput();
        } 
    }
}
