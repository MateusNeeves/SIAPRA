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
        foreach ($fornecedores as $i => $forn) {
            if ($forn->pais == "BRASIL"){
                // return response()->json($fab);
                $fornecedores[$i]->endereco = $forn->endereco . ($forn->numero != null ? ", " . $forn->numero : "") . ($forn->complemento != null ? ", " . $forn->complemento : "") . ", " . $forn->cidade . " - " . $forn->estado . ($forn->cep != null ? ", " . $forn->cep : "");
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
        
        // SALVANDO FABRICANTE
        if ($request->pais == "BRASIL"){
            Fornecedor::findOrFail($request->id_edit)->update([
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
            Fornecedor::findOrFail($request->id_edit)->update([
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
