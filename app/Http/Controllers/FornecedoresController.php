<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FornecedoresController extends Controller
{
    public function index(){
        $fornecedores = Fornecedor::all();
        $paises = Pais::all();
        return view('fornecedores/visualizar', ['fornecedores' => $fornecedores, 'paises' => $paises]);
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

        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fornecedor do Brasil deve ter um CNPJ')->with('modal', '#newModal')->withInput();     
    
        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fornecedor extrangeiro não deve possuir CNPJ')->with('modal', '#newModal')->withInput();     
    
        $fornecedor = new Fornecedor();
        
        $fornecedor->nome = $request->nome;
        $fornecedor->endereco = mb_strtoupper($request->endereco);
        $fornecedor->pais = $request->pais;
        $fornecedor->nome_contato = $request->nome_contato;
        $fornecedor->telefone = $request->telefone;
        $fornecedor->email = mb_strtolower($request->email);
        $fornecedor->site = $request->site;
        $fornecedor->cnpj = $request->cnpj;

        $fornecedor->save();
        return redirect()->route('fornecedores')->with('alert-success', 'Fornecedor cadastrado com sucesso');

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
        
        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fornecedor do Brasil deve ter um CNPJ')->with('modal', '#editModal')->withInput();     

        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fornecedor extrangeiro não deve possuir CNPJ')->with('modal', '#editModal')->withInput(); 
        
        // SALVANDO FABRICANTE
        Fornecedor::findOrFail($request->id_edit)->update([
            'nome' => $request->nome,
            'endereco' => mb_strtoupper($request->endereco),
            'pais' => $request->pais,
            'nome_contato' => $request->nome_contato,
            'telefone' => $request->telefone,
            'email' => mb_strtolower($request->email),
            'site' => $request->site,
            'cnpj' => $request->cnpj
        ]);
        return redirect()->route('fornecedores')->with('alert-success', 'Fornecedor editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        Fornecedor::find($request->id_delete)->delete();

        if ($request->soft == 'false'){
            try{
                Fornecedor::withTrashed()->find($request->id_delete)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Fornecedor excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Fornecedor, pois outras informações dependem dele. <br><br> Deseja Desativar esse Fornecedor ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Fornecedor desativado com sucesso');
    }
}
