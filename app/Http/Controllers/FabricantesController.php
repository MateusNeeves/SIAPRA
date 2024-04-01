<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Fabricante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class FabricantesController extends Controller
{
    public function index(){
        $fabricantes = Fabricante::all();
        return view('fabricantes/visualizar', ['fabricantes' => $fabricantes]);
    }

    public function register(){
        $paises = Pais::all();
        return view('fabricantes/cadastrar', ['paises' => $paises]);
    }

    public function store(Request $request){
        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => 'unique:fabricantes',
            'cnpj' => 'unique:fabricantes'],
            
            ['nome.unique' => 'Já existe um Fabricante com esse Nome',
            'cnpj.unique' => 'Já existe um Fabricante com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     
    
        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fabricante do Brasil deve ter um CNPJ')->withInput();     

        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fabricante extrangeiro não deve possuir CNPJ')->withInput();     

        $fabricantes = new Fabricante();
        
        $fabricantes->nome = $request->nome;
        $fabricantes->endereco = $request->endereco;
        $fabricantes->pais = $request->pais;
        $fabricantes->nome_contato = $request->nome_contato;
        $fabricantes->telefone = $request->telefone;
        $fabricantes->email = $request->email;
        $fabricantes->site = $request->site;
        $fabricantes->cnpj = $request->cnpj;

        $fabricantes->save();
        return redirect()->route('fabricantes')->with('alert-success', 'Fabricante cadastrado com sucesso');
    }

    public function edit($id){
        $fabricante = Fabricante::find($id);
        $paises = Pais::all();
        if ($fabricante)
            return view('fabricantes/editar', ['fabricante' => $fabricante, 'paises' => $paises]);
        else
            return redirect()->route('fabricantes')->with('alert-danger', 'Fabricante de id #' . $id . ' não encontrado.');
    }

    public function update(Request $request, $id){
        $validator = Validator::make(
            ['nome' => $request->nome,
            'cnpj' => $request->cnpj ?? ""],
            
            ['nome' => Rule::unique('fabricantes')->ignore($id),
            'cnpj' => Rule::unique('fabricantes')->ignore($id)],
            
            ['nome.unique' => 'Já existe um Fabricante com esse Nome',
            'cnpj.unique' => 'Já existe um Fabricante com esse CNPJ']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput(); 
        
        if ($request->pais == "BRASIL" && $request->cnpj == null)
            return redirect()->back()->with('alert-danger', 'Fabricante do Brasil deve ter um CNPJ')->withInput();     

        else if ($request->pais != "BRASIL" && $request->cnpj != null)
            return redirect()->back()->with('alert-danger', 'Fabricante extrangeiro não deve possuir CNPJ')->withInput(); 

        Fabricante::findOrFail($id)->update([
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'pais' => $request->pais,
            'nome_contato' => $request->nome_contato,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'site' => $request->site,
            'cnpj' => $request->cnpj
        ]);
        return redirect()->route('fabricantes')->with('alert-success', 'Fabricante editado com sucesso');
    }

    public function destroy(Request $request){
        try{
            Fabricante::findOrFail($request->id)->delete();
    
            return redirect()->route('fabricantes')->with('alert-success', 'Fabricantes exclúido com sucesso');
        }
        catch (\Exception $exception) {
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro ao deletar o fabricante: ' . $exception->getMessage())->withInput(); 
        }
    }
}
