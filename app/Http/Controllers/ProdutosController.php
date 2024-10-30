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

function get_infos_store(){
    $tipos = Tipo_Produto::all();
    $fabricantes = Fabricante::all();
    $fornecedores = Fornecedor::all();
    
    return ['tipos' =>  $tipos, 'fabricantes' => $fabricantes, 'fornecedores' => $fornecedores];
}

function get_infos_view(Request $request){
    $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) WHERE P.ID = ?', [$request->id_view])[0];
            
    $forns = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
    $fornecedores = [];
    foreach ($forns as $fornecedor)
        $fornecedores[] = $fornecedor->nome;

    $fabs = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
    $fabricantes = [];
    foreach ($fabs as $fabricante)
        $fabricantes[] = $fabricante->nome;


    $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE FROM PRODUTOS_LOTE L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ? ORDER BY L.DATA_VALIDADE ASC', [$produto->id]);
    
    $lotes = json_decode(json_encode($lotes), true);
    
    return ['produtoV' => $produto, 'fabricantesV' => $fabricantes, 'fornecedoresV' => $fornecedores, 'lotesV' => $lotes];
}

class ProdutosController extends Controller
{
    public function index(){
        $produtos = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID)');
        
        $produtos = json_decode(json_encode($produtos), true);
        return view('produtos/visualizar', ['produtos' => $produtos]);
    }

    public function register(){
        $registerInfos = array_merge(get_infos_store(), [
            'modal' => ['#newModal']
        ]);
        // return response()->json($registerInfos);

        return redirect()->back()->with($registerInfos);
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE

        $validator = Validator::make(
            $request->all(),
            
            ['nome' => 'unique:produtos'],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );

        if ($validator->fails()){
            $storeInfos = array_merge(get_infos_store(), [
                'modal' => ['#newModal'],
                'alert-danger' => $validator->messages()->first()
            ]);
    
            return redirect()->back()->with($storeInfos)->withInput();     
        }
        
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
        $viewInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal']
        ]);

        return redirect()->back()->with($viewInfos)->withInput(); 
    }

    public function edit(Request $request){
        $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) WHERE P.ID = ?', [$request->id_edit])[0];
                
        $fornecedores = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fornSelected = [];
        foreach ($fornecedores as $fornecedor)
            $fornSelected[] = $fornecedor->nome;

        $fabricantes = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
        $fabSelected = [];
        foreach ($fabricantes as $fabricante)
            $fabSelected[] = $fabricante->nome;

        $editInfos = array_merge(get_infos_store(), [
            'modal' => ['#editModal'],
            'produto' => $produto,
            'fabSelected' => $fabSelected,
            'fornSelected' => $fornSelected,
        ]);

        return redirect()->back()->with($editInfos)->withInput(); 

    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE
        $validator = Validator::make(
            $request->all(),
            
            ['nome' => Rule::unique('produtos')->ignore($request->id_edit)],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome']
        );
        
        if ($validator->fails()){
            $updateInfos = array_merge(get_infos_store(), [
                'modal' => ['#editModal'],
                'alert-danger' => $validator->messages()->first()
            ]);

            return redirect()->back()->with($updateInfos)->withInput();   
        }
    
        
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

    public function view_expired(){
        $now = Carbon::createFromFormat("H:i:s", date('H:i:s'));

        $lotes_vencidos = DB::select('SELECT L.ID, P.NOME AS PRODUTO, F.NOME AS FABRICANTE, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, l.DATA_VALIDADE  FROM PRODUTOS P INNER JOIN PRODUTOS_LOTE L ON (P.ID = L.ID_PRODUTO) INNER JOIN FABRICANTES F ON (F.ID = L.ID_FABRICANTE) WHERE L.DATA_VALIDADE < ? AND L.QTD_ITENS_ESTOQUE > 0', [$now]);
        $lotes_vencidos = json_decode(json_encode($lotes_vencidos), true);

        return redirect()->back()->with(['lotes_vencidos' => $lotes_vencidos, 'modal' => ['#viewExpModal']])->withInput();
    }

    public function destroy_expired(Request $request){
        try{
            DB::beginTransaction();

            $lote = Produto_Lote::findOrFail($request->id_exp);

            $mov = new Produto_Mov;
    
            $mov->id_produtos_lote = $lote->id;
            $mov->id_destino = Dest_Produto::where('nome', 'Vencido')->get()[0]->id;

            $mov->qtd_itens_movidos = $lote->qtd_itens_estoque;
            $mov->hora_mov = Carbon::createFromFormat("H:i:s", date('H:i:s'));

            $mov->save();

            $lote->update(['qtd_itens_estoque' => 0]);

            DB::commit();
            return redirect()->back()->with('alert-success', 'Confirmação de Retirada realizada com sucesso com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function register_lote(Request $request){
        $fabricantes = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$request->id_view]);

        $fornecedores = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$request->id_view]);

        $fabricantes = json_decode(json_encode($fabricantes), true);
        $fornecedores = json_decode(json_encode($fornecedores), true);

        $register_loteInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#loteModal'],
            'fornecedores_lote' => $fornecedores,
            'fabricantes_lote' => $fabricantes
        ]);

        return redirect()->back()->with($register_loteInfos)->withInput();
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

        $store_loteInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal'],
            'id_view_backup' => $request->id_view,
            'alert-success' => 'Lote do Produto Adicionado com Sucesso'
        ]);

        
        return redirect()->back()->with($store_loteInfos);
    }

    public function view_print(Request $request){
        $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE FROM PRODUTOS_LOTE L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ? ORDER BY L.DATA_VALIDADE ASC', [$request->id_view]);
        $lotes = json_decode(json_encode($lotes), true);
        return redirect()->back()->with(['modal' => ['#selecLoteModal'],'lotesP' => $lotes, 'title_modal' => 'Selecione o lote para imprimir rótulo:', 'route_modal' => '']);
    }

    public function make_mov(Request $request){
        $now = Carbon::createFromFormat("H:i:s", date('H:i:s'));
        $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE FROM PRODUTOS_LOTE L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ? AND L.QTD_ITENS_ESTOQUE > 0 AND L.DATA_VALIDADE > ? ORDER BY L.DATA_VALIDADE ASC', [$request->id_view, $now]);
        $lotes = json_decode(json_encode($lotes), true);

        $make_movInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#selecLoteModal'],
            'lotesP' => $lotes,
            'title_modal' => 'Selecione o lote para retirada:',
            'route_modal' => '.register_mov'

        ]);

        return redirect()->back()->with($make_movInfos)->withInput();
    }
    
    public function register_mov(Request $request){
        $dest_produtos = Dest_Produto::all();

        $register_movInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#newMovModal'],
            'dest_produtos' => $dest_produtos,
            'qtd_estoque_lote' => $request->qtd_estoque_lote,
            'id_lote' => $request->id_lote
        ]);

        return redirect()->back()->with($register_movInfos)->withInput();
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

            $store_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal'],
                'id_view_backup' => $request->id_view,
                'alert-success' => 'Retirada realizada com sucesso com sucesso'
            ]);

            return redirect()->back()->with($store_movInfos);
        }
        catch (\Exception $exception) {
            DB::rollback();

            $store_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal'],
                'id_view_backup' => $request->id_view,
                'alert-danger' => 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage()
            ]);

            return redirect()->back()->with($store_movInfos)->withInput();
        }
    }

    public function view_mov(Request $request){
        $lotes_entrada = DB::select('SELECT ID, QTD_ITENS_RECEBIDOS, DATA_ENTREGA FROM PRODUTOS_LOTE WHERE ID_PRODUTO = ? ORDER BY DATA_ENTREGA', [$request->id_view]);
        $lotes_entrada = json_decode(json_encode($lotes_entrada), true);
        
        $lotes_saida = [];
        foreach ($lotes_entrada as $i => $lote_entrada) {
            $lotes_saida[$i] = DB::select('SELECT M.QTD_ITENS_MOVIDOS, M.HORA_MOV, D.NOME FROM PRODUTOS_MOV M INNER JOIN DEST_PRODUTOS D ON (M.ID_DESTINO = D.ID) WHERE ID_PRODUTOS_LOTE = ? ORDER BY HORA_MOV', [$lote_entrada['id']]);
            $lotes_saida[$i] = json_decode(json_encode($lotes_saida[$i]), true);
        }

        $view_movInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#viewMovModal'],
            'lotes_saida' => $lotes_saida,
            'lotes_entrada' => $lotes_entrada
        ]);

        return redirect()->back()->with($view_movInfos)->withInput();
    }
}
