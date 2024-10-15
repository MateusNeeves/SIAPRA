<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Dest_Produto;
use App\Models\Produto_Fab;
use App\Models\Produto_Lote;
use App\Models\Produto_Forn;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Fornecedor;
use App\Models\Produto_Mov;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutosController extends Controller
{
    public function index(){
        $produtos = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID)');
        
        // ALTERANDO FORMATO DOS FABRICANTES E FORNECEDORES PARA DISPLAY
        foreach ($produtos as $idx => $produto) {
            $fabricantes = DB::select('SELECT NOME FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
            $fab = "";
            foreach ($fabricantes as $i => $fabricante)
                $fab .= $fabricante->nome . ($i == count($fabricantes) - 1 ? '' : '<br>');

            $fornecedores = DB::select('SELECT NOME FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
            $forn = "";
            foreach ($fornecedores as $i => $fornecedor)
                $forn .= $fornecedor->nome . ($i == count($fornecedores) - 1 ? '' : '<br>');
            
            $produtos[$idx]->fabricantes = $fab;
            $produtos[$idx]->fornecedores = $forn;
        }
        
        $tipos = Tipo_Produto::all();
        $fabricantes = Fabricante::all();
        $fornecedores = Fornecedor::all();
        
        $produtos = json_decode(json_encode($produtos), true);
        return view('produtos/visualizar', ['produtos' => $produtos, 'tipos' => $tipos, 'fabricantes' => $fabricantes, 'fornecedores' => $fornecedores]);
    }

    public function register(){
        return redirect()->back()->with('modal', '#newModal');
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE

        $validator = Validator::make(
            $request->all(),
            
            ['nome' => 'unique:produtos'],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
        
        try{
            DB::beginTransaction();

            // ADICIONANDO PRODUTO

            $produto = new Produto;
    
            $produto->nome = $request->nome;
            $produto->descricao = $request->descricao;
            $produto->id_tipo = Tipo_Produto::where('nome', $request->tipo)->first()->id;
            $produto->qtd_aceitavel = $request->qtd_aceitavel;
            $produto->qtd_minima = $request->qtd_minima;
    
            $produto->save();

            // ADICIONANDO FABRICANTES

            foreach ((array) $request->fabricantes as $i => $fabricante) {
                $id_fabricante = Fabricante::where('nome', $fabricante)->first()->id;
                    
                $produto_fab[$i] = new Produto_Fab;
                
                $produto_fab[$i]->id_produto = $produto->id;
                $produto_fab[$i]->id_fabricante = $id_fabricante;
                
                $produto_fab[$i]->save();
            }

            // ADICIONANDO FORNECEDORES

            foreach ((array) $request->fornecedores as $i => $fornecedor) {
                $id_fornecedor = Fornecedor::where('nome', $request->fornecedores[$i])->first()->id;
                    
                $produto_forn[$i] = new Produto_Forn;
                
                $produto_forn[$i]->id_produto = $produto->id;
                $produto_forn[$i]->id_fornecedor = $id_fornecedor;
                
                $produto_forn[$i]->save();
            }
            
            DB::commit();
            return redirect()->back()->with('alert-success', 'Produto cadastrado com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }

    }

    public function view(Request $request){
        $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) WHERE P.ID = ?', [$request->id_view])[0];
                
        $forns = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fornecedores = [];
        foreach ($forns as $fornecedor)
            $fornecedores[] = $fornecedor->nome;

        $fabs = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fabricantes = [];
        foreach ($fabs as $fabricante)
            $fabricantes[] = $fabricante->nome;

        $lotes = Produto_Lote::where('id_produto', $produto->id)->orderBy('data_validade', 'asc')->get();
        
        return redirect()->back()->with(['produtoV' => $produto, 'modal' => '#viewModal', 'fabricantesV' => $fabricantes, 'fornecedoresV' => $fornecedores, 'lotesV' => $lotes])->withInput(); 

    }

    public function edit(Request $request){
        $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) WHERE P.ID = ?', [$request->id_edit])[0];
                
        $forns = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fornecedores = [];
        foreach ($forns as $fornecedor)
            $fornecedores[] = $fornecedor->nome;

        $fabs = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fabricantes = [];
        foreach ($fabs as $fabricante)
            $fabricantes[] = $fabricante->nome;

        return redirect()->back()->with(['produto' => $produto, 'modal' => '#editModal', 'fabricantes' => $fabricantes, 'fornecedores' => $fornecedores])->withInput(); 

    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE
        $validator = Validator::make(
            $request->all(),
            
            ['nome' => Rule::unique('produtos')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );
        
        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput(); 
    
        
        try{
            DB::beginTransaction();

            // ATUALIZANDO PRODUTO
            
                $produto = Produto::findOrFail($request->id_edit);
                
                $produto->update([
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'id_tipo' => Tipo_Produto::where('nome', $request->tipo)->get()[0]->id,
                    'qtd_aceitavel' => $request->qtd_aceitavel,
                    'qtd_minima' => $request->qtd_minima
                ]);

            // ATUALIZANDO FORNECEDORES
            
                // lista desatualizada de fornecedores
                $forns = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
                $old_forns = [];
                foreach ($forns as $fornecedor)
                    $old_forns[] = $fornecedor->nome;
            
                $new_forns = array_diff($request->fornecedores ?? [], $old_forns ?? []);
                $removed_forns = array_diff($old_forns ?? [], $request->fornecedores ?? []);
            
                // REMOVENDO FORNECEDORES
                Produto_Forn::join('fornecedores', 'produtos_forn.id_fornecedor', '=', 'fornecedores.id')->where('produtos_forn.id_produto', $produto->id)->whereIn('fornecedores.nome', $removed_forns)->delete();

                // ADICIONANDO FORNECEDORES
                foreach ($new_forns as $i => $fornecedor) {
                    $id_fornecedor = Fornecedor::where('nome', $fornecedor)->first()->id;
                        
                    $produto_forn[$i] = new Produto_Forn;
                    
                    $produto_forn[$i]->id_produto = $produto->id;
                    $produto_forn[$i]->id_fornecedor = $id_fornecedor;
                    
                    $produto_forn[$i]->save();
                }
            
            /// ATUALIZANDO FABRICANTES
            
                // lista desatualizada de fabricantes
                $fabs = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
                $old_fabs = [];
                foreach ($fabs as $fabricante)
                    $old_fabs[] = $fabricante->nome;
            
                $new_fabs = array_diff($request->fabricantes ?? [], $old_fabs ?? []);
                $removed_fabs = array_diff($old_fabs ?? [], $request->fabricantes ?? []);
            
                // REMOVENDO FABRICANTES
                Produto_Fab::join('fabricantes', 'produtos_fab.id_fabricante', '=', 'fabricantes.id')->where('produtos_fab.id_produto', $produto->id)->whereIn('fabricantes.nome', $removed_fabs)->delete();

                // ADICIONANDO FABRICANTES
                foreach ($new_fabs as $i => $fabricante) {
                    $id_fabricante = Fabricante::where('nome', $fabricante)->first()->id;
                        
                    $produto_fab[$i] = new Produto_Fab;
                    
                    $produto_fab[$i]->id_produto = $produto->id;
                    $produto_fab[$i]->id_fabricante = $id_fabricante;
                    
                    $produto_fab[$i]->save();
                }

            DB::commit();
            return redirect()->back()->with('alert-success', 'Produto editado com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na atualização no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();

            Produto_Fab::where('id_produto', $request->id_delete)->delete();
            Produto_Forn::where('id_produto', $request->id_delete)->delete();
            Produto::find($request->id_delete)->delete();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Produto excluído com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();

            return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Produto, pois outras informações dependem dele.')->withInput();
        } 
    }

    public function register_lote(Request $request){
        $fabricantes = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$request->id_view]);

        $fornecedores = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$request->id_view]);

        $fabricantes = json_decode(json_encode($fabricantes), true);
        $fornecedores = json_decode(json_encode($fornecedores), true);

        return redirect()->back()->with(['fabricantes_lote' => $fabricantes, 'fornecedores_lote' => $fornecedores, 'modal' => '#loteModal'])->withInput();
    }

    public function store_lote(Request $request){
        // VERIFICANDO UNICIDADE
         
        $id_fabricante = Fabricante::where('nome', $request->fabricante)->get()[0]->id;
        $id_fornecedor = Fornecedor::where('nome', $request->fornecedor)->get()[0]->id;

        $lote = Produto_Lote::where('id_produto', $request->id_view)->where('id_fabricante', $id_fabricante)->where('lote_fabricante', $request->lote_fabricante)->first();
        
        if ($lote){
            // ATUALIZANDO QTD LOTE DO PRODUTO
            Produto_Lote::findOrFail($lote->id)->update([
                'qtd_itens_recebidos' => $lote->qtd_itens_recebidos + $request->qtd_itens_recebidos,
                'qtd_itens_estoque' => $lote->qtd_itens_estoque + $request->qtd_itens_recebidos,
                'preco' => $lote->preco + $request->preco
            ]);
        }
        else{
            // ADICIONANDO LOTE DO PRODUTO

            $lote = new Produto_Lote;
    
            $lote->id_produto = $request->id_view;
            $lote->id_fabricante = $id_fabricante;
            $lote->lote_fabricante = $request->lote_fabricante;
            $lote->id_fornecedor = $id_fornecedor;
            $lote->qtd_itens_recebidos = $request->qtd_itens_recebidos;
            $lote->qtd_itens_estoque = $request->qtd_itens_recebidos;
            $lote->preco = $request->preco;
            $lote->data_entrega = $request->data_entrega;
            $lote->data_validade = $request->data_validade;
            
            $lote->save();
        }

        $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) WHERE P.ID = ?', [$request->id_view])[0];
                
        $forns = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fornecedores = [];
        foreach ($forns as $fornecedor)
            $fornecedores[] = $fornecedor->nome;

        $fabs = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fabricantes = [];
        foreach ($fabs as $fabricante)
            $fabricantes[] = $fabricante->nome;

        $lotes = Produto_Lote::where('id_produto', $produto->id)->get();
        
        return redirect()->back()->with(['alert-success' => 'Lote do Produto Adicionado com Sucesso', 'modal' => '#viewModal', 'id_view_backup' => $request->id_view, 'produtoV' => $produto, 'fabricantesV' => $fabricantes, 'fornecedoresV' => $fornecedores, 'lotesV' => $lotes]);
    }
    public function view_print(Request $request){
        $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE FROM PRODUTOS_LOTE L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ?', [$request->id_view]);
        $lotes = json_decode(json_encode($lotes), true);
        return redirect()->back()->with(['modal' => '#selecLoteModal','lotesP' => $lotes, 'title_modal' => 'Selecione o lote para imprimir rótulo:', 'route_modal' => '']);
    }

    public function make_mov(Request $request){
        $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE FROM PRODUTOS_LOTE L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ? AND L.QTD_ITENS_ESTOQUE > 0', [$request->id_view]);
        $lotes = json_decode(json_encode($lotes), true);
        return redirect()->back()->with(['modal' => '#selecLoteModal','lotesP' => $lotes, 'title_modal' => 'Selecione o lote para retirada:', 'route_modal' => '.register_mov']);
    }
    
    public function register_mov(Request $request){
        $dest_produtos = Dest_Produto::all();

        return redirect()->back()->with(['id_lote' => $request->id_lote, 'qtd_estoque_lote' => $request->qtd_estoque_lote, 'dest_produtos' => $dest_produtos, 'modal' => '#newMovModal'])->withInput();
    }

    public function store_mov(Request $request){
        try{
            DB::beginTransaction();

            $mov = new Produto_Mov;
    
            $mov->id_produtos_lote = $request->id_lote;
            $mov->id_destino = $request->destino;
            $mov->qtd_itens_movidos = $request->qtd_itens_movidos;
            $mov->hora_mov = Carbon::createFromFormat("H:i:s", date('H:i:s'));

            $mov->save();

            Produto_Lote::findOrFail($request->id_lote)->decrement('qtd_itens_estoque', $request->qtd_itens_movidos);
            
            DB::commit();
            return redirect()->back()->with('alert-success', 'Retirada realizada com sucesso com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }

    }
}
