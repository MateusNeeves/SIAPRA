<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(){
        $clientes = Cliente::all();
        return view('clientes/visualizar', ['clientes' => $clientes]);
    }

    // public function register(){
    //     return view('clientes/cadastrar');
    // }

    public function store(Request $request){
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

        $cliente = new Cliente;

        $cliente->cnpj = $request->cnpj;
        $cliente->razao_social = $request->razao_social;
        $cliente->nome_fantasia = $request->nome_fantasia;
        $cliente->end_logradouro = $request->end_logradouro;
        $cliente->end_complemento = $request->end_complemento;
        $cliente->estado = $request->estado;
        $cliente->cidade = $request->cidade;
        $cliente->bairro = $request->bairro;
        $cliente->cep = $request->cep;
        $cliente->tempo_transp = $request->tempo_transp;

        $cliente->save();

        return redirect()->route('clientes')->with('alert-success', 'Cliente cadastrado com sucesso');
    }

    public function edit($id){
        $cliente = Cliente::find($id);
        if ($cliente)
            return view('clientes/editar', ['cliente' => $cliente]);
        else
            return redirect()->route('clientes')->with('alert-danger', 'Cliente de id #' . $id . ' não encontrado.');
    }

    public function update(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['cnpj' => Rule::unique('clientes')->ignore($request->id),
            'razao_social' => Rule::unique('clientes')->ignore($request->id),
            'nome_fantasia' => Rule::unique('clientes')->ignore($request->id)],
            
            ['cnpj.unique' => 'Já existe um cliente com esse CNPJ',
            'razao_social.unique' => 'Já existe um Cliente com essa Razão Social',
            'nome_fantasia.unique' => 'Já existe um Cliente com esse Nome Fantasia']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     

        Cliente::findOrFail($request->id)->update([
            'cnpj' => $request->cnpj,
            'razao_social' => $request->razao_social,
            'nome_fantasia' => $request->nome_fantasia,
            'end_logradouro' => $request->end_logradouro,
            'end_complemento' => $request->end_complemento,
            'estado' => $request->estado,
            'cidade' => $request->cidade,
            'bairro' => $request->bairro,
            'cep' => $request->cep,
            'tempo_transp' => $request->tempo_transp
        ]);
        return redirect()->route('clientes')->with('alert-success', 'Cliente editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        Cliente::find($request->id)->delete();

        if ($request->soft == 'false'){
            try{
                Cliente::withTrashed()->find($request->id)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Cliente excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Cliente, pois outras informações dependem dele. <br><br> Deseja Desativar esse Cliente ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Cliente desativado com sucesso');
    }
}
