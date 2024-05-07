<?php

namespace App\Http\Controllers;

use App\Models\Produto_Fab;
use App\Models\Produto_Forn;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Fornecedor;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutosController extends Controller
{
    public function index(){
        $produtos = collect(DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA
                                        FROM PRODUTOS P 
                                        INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID)'));
        $produtos = json_decode(json_encode($produtos), true);
        $tipos = Tipo_Produto::all();
        $fabricantes = Fabricante::all();
        $fornecedores = Fornecedor::all();

        return view('produtos/visualizar', ['produtos' => $produtos, 'tipos' => $tipos, 'fabricantes' => $fabricantes, 'fornecedores' => $fornecedores]);
    }

    public function store(Request $request){
        // return response()->json($request->fornecedores == null);
        $id_tipo = Tipo_Produto::where('nome', $request->tipo)->first()->id;
        $validator = Validator::make(
            $request->all(),
            
            ['nome' => 'unique:produtos'],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
        
        try{
            DB::beginTransaction();

            $produto = new Produto;
    
            $produto->nome = $request->nome;
            $produto->descricao = $request->descricao;
            $produto->id_tipo = $id_tipo;
            $produto->qtd_aceitavel = $request->qtd_aceitavel;
            $produto->qtd_minima = $request->qtd_minima;
    
            $produto->save();

            if ($request->fabricantes){
                for($i = 0 ; $i < count($request->fabricantes) ; $i++){
                    $id_fabricante = Fabricante::where('nome', $request->fabricantes[$i])->first()->id;
                    
                    $produto_fab[$i] = new Produto_Fab;

                    $produto_fab[$i]->id_produto = $produto->id;
                    $produto_fab[$i]->id_fabricante = $id_fabricante;

                    $produto_fab[$i]->save();
                }
            }

            if ($request->fornecedores){
                for($i = 0 ; $i < count($request->fornecedores) ; $i++){
                    $id_fornecedor = Fornecedor::where('nome', $request->fornecedor[$i])->first()->id;
                    
                    $produto_forn[$i] = new Produto_Forn;
                    
                    $produto_forn[$i]->id_produto = $produto->id;
                    $produto_forn[$i]->id_fornecedor = $id_fornecedor;

                    $produto_forn[$i]->save();
                }
            }
            
            return redirect()->back()->with('alert-success', 'Produto cadastrado com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }

    }

    public function update(Request $request){
        $id_tipo = Tipo_Produto::where('nome', $request->nome)->get()[0]->id;
        $validator = Validator::make(
            $request->all(),
            
            ['nome' => Rule::unique('produtos')->ignore($request->id)],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput(); 

        Produto::findOrFail($request->id)->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'id_tipo' => $id_tipo,
            'qtd_aceitavel' => $request->qtd_aceitavel,
            'qtd_minima' => $request->qtd_minima
        ]);
        return redirect()->route('produtos')->with('alert-success', 'Produto editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        Produto::find($request->id)->delete();

        if ($request->soft == 'false'){
            try{
                Produto::withTrashed()->find($request->id)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Produto excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Produto, pois outras informações dependem dele. <br><br> Deseja Desativar esse Produto ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Produto desativado com sucesso');
    }
}
