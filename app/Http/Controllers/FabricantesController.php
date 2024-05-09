<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Fabricante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FabricantesController extends Controller
{
    public function index(){
        $fabricantes = Fabricante::all();
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
    
        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fabricante do Brasil deve ter um CNPJ')->with('modal', '#newModal')->withInput();     

        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fabricante extrangeiro não deve possuir CNPJ')->with('modal', '#newModal')->withInput();     

        // SALVANDO FABRICANTE
        $fabricantes = new Fabricante();
        
        $fabricantes->nome = $request->nome;
        $fabricantes->endereco = mb_strtoupper($request->endereco);
        $fabricantes->pais = $request->pais;
        $fabricantes->nome_contato = $request->nome_contato;
        $fabricantes->telefone = $request->telefone;
        $fabricantes->email = mb_strtolower($request->email);
        $fabricantes->site = $request->site;
        $fabricantes->cnpj = $request->cnpj;

        $fabricantes->save();
        return redirect()->route('fabricantes')->with('alert-success', 'Fabricante cadastrado com sucesso');
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
        
        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fabricante do Brasil deve ter um CNPJ')->with('modal', '#editModal')->withInput();     

        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fabricante extrangeiro não deve possuir CNPJ')->with('modal', '#editModal')->withInput(); 
        
        // SALVANDO FABRICANTE
        Fabricante::findOrFail($request->id_edit)->update([
            'nome' => $request->nome,
            'endereco' => mb_strtoupper($request->endereco),
            'pais' => $request->pais,
            'nome_contato' => $request->nome_contato,
            'telefone' => $request->telefone,
            'email' => mb_strtolower($request->email),
            'site' => $request->site,
            'cnpj' => $request->cnpj
        ]);
        return redirect()->route('fabricantes')->with('alert-success', 'Fabricante editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        Fabricante::find($request->id_delete)->delete();

        if ($request->soft == 'false'){
            try{
                Fabricante::withTrashed()->find($request->id_delete)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Fabricante excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Fabricante, pois outras informações dependem dele. <br><br> Deseja Desativar esse Fabricante ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Fabricante desativado com sucesso');
    }
}
