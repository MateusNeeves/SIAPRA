<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\Acao;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Fornecedor;
use App\Models\Produto_Fab;
use App\Models\Dest_Produto;
use App\Models\Produto_Forn;
use App\Models\Tipo_Produto;
use Illuminate\Http\Request;
use App\Models\Produto_Mov_In;
use App\Models\Produto_Mov_Out;
use App\Models\Unidade_Medida;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

function get_infos_store(){
    $tipos = Tipo_Produto::all();
    $fabricantes = Fabricante::all();
    $fornecedores = Fornecedor::all();
    $unidades_medida = Unidade_Medida::all();
    
    return ['tipos' =>  $tipos, 'fabricantes' => $fabricantes, 'fornecedores' => $fornecedores, 'unidades_medida' => $unidades_medida];
}

function get_infos_view(Request $request){
    $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA, P.QUARENTENA, U.NOME AS UNIDADE_MEDIDA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) INNER JOIN UNIDADES_MEDIDA U ON (U.ID = P.ID_UNIDADE_MEDIDA) WHERE P.ID = ?', [$request->id_view])[0];
            
    $forns = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$produto->id]);
    $fornecedores = [];
    foreach ($forns as $fornecedor)
        $fornecedores[] = $fornecedor->nome;

    $fabs = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$produto->id]);
    $fabricantes = [];
    foreach ($fabs as $fabricante)
        $fabricantes[] = $fabricante->nome;


    $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE, L.QUARENTENA FROM PRODUTOS_MOV_IN L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ? ORDER BY L.ID ASC', [$produto->id]);
    
    $lotes = json_decode(json_encode($lotes), true);
    
    return ['produtoV' => $produto, 'fabricantesV' => $fabricantes, 'fornecedoresV' => $fornecedores, 'lotesV' => $lotes];
}

class ProdutosController extends Controller
{
    public function index(){
        $produtos = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA, P.QUARENTENA, U.NOME AS UNIDADE_MEDIDA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) INNER JOIN UNIDADES_MEDIDA U ON (U.ID = P.ID_UNIDADE_MEDIDA)');
        
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
            
            ['nome' => 'unique:produtos',
            'quarentena' => 'in:Sim,Não',],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome',
            'quarentena.in' => 'Opção inválida para Quarentena.']
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
            $produto->quarentena = $request->quarentena;
            $produto->id_unidade_medida = Unidade_Medida::where('nome', $request->unidade_medida)->first()->id;
    
            $produto->save();

            // ADICIONANDO FABRICANTES

            $fabricantesLog = "";

            if (!empty($request->fabricantes)){
                $fabricantes = Fabricante::select('id', 'nome')->whereIn('nome', $request->fabricantes)->get()->toArray();
    
                foreach ($fabricantes as $i => $fabricante) {                    
                    $produto_fab[$i] = new Produto_Fab;
                    
                    $produto_fab[$i]->id_produto = $produto->id;
                    $produto_fab[$i]->id_fabricante = $fabricante['id'];
                    
                    $produto_fab[$i]->save();
    
                    $fabricantesLog .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$fabricante['id']}, Nome: {$fabricante['nome']}\n";
                }
            }


            // ADICIONANDO FORNECEDORES

            $fornecedoresLog = "";

            if (!empty($request->fornecedores)){
                $fornecedores = Fornecedor::select('id', 'nome')->whereIn('nome', $request->fornecedores ?? [])->get()->toArray();

                foreach ($fornecedores as $fornecedor) {                   
                    $produto_forn[$i] = new Produto_Forn;
                    
                    $produto_forn[$i]->id_produto = $produto->id;
                    $produto_forn[$i]->id_fornecedor = $fornecedor['id'];
                    
                    $produto_forn[$i]->save();

                    $fornecedoresLog .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$fornecedor['id']}, Nome: {$fornecedor['nome']}\n";
                }
            }

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Produto adicionado:\n" .
                "- ID do Produto: {$produto->id}\n" .
                "- Nome: {$produto->nome}\n" .
                "- Descrição: {$produto->descricao}\n" .
                "- Tipo: ID: {$produto->id_tipo}, Nome: {$request->tipo}\n" .
                "- Qtd. Aceitável: {$produto->qtd_aceitavel}\n" .
                "- Qtd. Mínima: {$produto->qtd_minima}\n" .
                "- Fabricantes: " . ($fabricantesLog === "" ? "(não informado)\n" : "\n".$fabricantesLog) .
                "- Fornecedores: " . ($fornecedoresLog === "" ? "(não informado)\n" : "\n".$fornecedoresLog) .
                "- Quarentena: {$produto->quarentena}\n" .
                "- Unidade de Medida: ID: {$produto->id_unidade_medida}, Nome: {$request->unidade_medida}\n";

            $log->save();
            
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
        $produto = DB::select('SELECT P.ID, P.NOME, P.DESCRICAO, T.NOME AS TIPO, P.QTD_ACEITAVEL, P.QTD_MINIMA, P.QUARENTENA, U.NOME AS UNIDADE_MEDIDA FROM PRODUTOS P INNER JOIN TIPOS_PRODUTOS T ON (P.ID_TIPO = T.ID) INNER JOIN UNIDADES_MEDIDA U ON (U.ID = P.ID_UNIDADE_MEDIDA) WHERE P.ID = ?', [$request->id_edit])[0];
                
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
            
            ['nome' => Rule::unique('produtos')->ignore($request->id_edit),
            'quarentena' => 'in:Sim,Não',],
            
            ['nome.unique' => 'Já existe um Produto com esse Nome',
            'quarentena.in' => 'Opção inválida para Quarentena.']
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

                $produtoAntes = $produto->toArray();
                
                $produto->update([
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'id_tipo' => Tipo_Produto::where('nome', $request->tipo)->get()[0]->id,
                    'qtd_aceitavel' => $request->qtd_aceitavel,
                    'qtd_minima' => $request->qtd_minima,
                    'quarentena' => $request->quarentena,
                    'id_unidade_medida' => Unidade_Medida::where('nome', $request->unidade_medida)->get()[0]->id
                ]);

                $produtoDepois = $produto->refresh()->toArray();

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

                $new_forns = Fornecedor::select('id', 'nome')->whereIn('nome', $new_forns)->get()->toArray();
                $removed_forns = Fornecedor::select('id', 'nome')->whereIn('nome', $removed_forns)->get()->toArray();

                foreach ($new_forns as $i => $fornecedor) {
                    $produto_forn[$i] = new Produto_Forn;
                    
                    $produto_forn[$i]->id_produto = $produto->id;
                    $produto_forn[$i]->id_fornecedor = $fornecedor['id'];
                    
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

                $new_fabs = Fabricante::select('id', 'nome')->whereIn('fabricantes.nome', $new_fabs)->get()->toArray();
                $removed_fabs = Fabricante::select('id', 'nome')->whereIn('nome', $removed_fabs)->get()->toArray();

                foreach ($new_fabs as $fabricante) {                     
                    $produto_fab = new Produto_Fab;
                    
                    $produto_fab->id_produto = $produto->id;
                    $produto_fab->id_fabricante = $fabricante['id'];
                    
                    $produto_fab->save();
                }

            // CADASTRANDO LOG
                if (array_diff_assoc($produtoAntes, $produtoDepois) != [] || $new_fabs != [] || $removed_fabs != [] || $new_forns != [] || $removed_forns != []) {

                    $log = new Log();

                    $log->id_user = Auth::user()->id;
                    $log->id_acao = Acao::where('descricao', 'Editar Produto')->first()["id"];
                    $log->tipo = "Info";
                    $log->data_hora = now();
                    $log->descricao = 
                        "Produto editado:\n" .
                        "- ID do Produto: {$produtoAntes['id']}\n" .
                        "- Nome do Produto: {$produtoAntes['nome']}\n\n" .
                        "Campos alterados:\n";

                    foreach ($produtoDepois as $campo => $valor) {
                        if ($valor != ($produtoAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($produtoAntes[$campo] === null || $produtoAntes[$campo] === '' ? '(não informado)' : $produtoAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }

                    // Fabricantes Adicionados
                    if (!empty($new_fabs)) {
                        $log->descricao .= "- Fabricantes Adicionados:\n";
                        foreach ($new_fabs as $new_fab) {
                            $log->descricao .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$new_fab['id']}, Nome: {$new_fab['nome']}\n";
                        }
                    }

                    // Fabricantes Removidos
                    if (!empty($removed_fabs)) {
                        $log->descricao .= "- Fabricantes Removidos:\n";
                        foreach ($removed_fabs as $removed_fab) {
                            $log->descricao .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$removed_fab['id']}, Nome: {$removed_fab['nome']}\n";
                        }
                    }

                    // Fornecedores Adicionados
                    if (!empty($new_forns)) {
                        $log->descricao .= "- Fornecedores Adicionados:\n";
                        foreach ($new_forns as $new_forn) {
                            $log->descricao .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$new_forn['id']}, Nome: {$new_forn['nome']}\n";
                        }
                    }

                    // Fornecedores Removidos
                    if (!empty($removed_forns)) {
                        $log->descricao .= "- Fornecedores Removidos:\n";
                        foreach ($removed_forns as $removed_forn) {
                            $log->descricao .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$removed_forn['id']}, Nome: {$removed_forn['nome']}\n";
                        }
                    }
                    
                    $log->save();
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

            $fabricantes = DB::select('SELECT ID, NOME FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOs_FAB WHERE ID_PRODUTO = ?)', [$request->id_delete]);
            Produto_Fab::where('id_produto', $request->id_delete)->delete();

            $fornecedores = DB::select('SELECT ID, NOME FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOs_FORN WHERE ID_PRODUTO = ?)', [$request->id_delete]);
            Produto_Forn::where('id_produto', $request->id_delete)->delete();

            $fabricantesLog = "";
            foreach ($fabricantes as $fabricante) {
                $fabricantesLog .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$fabricante->id}, Nome: {$fabricante->nome}\n";
            }

            $fornecedoresLog = "";
            foreach ($fornecedores as $fornecedor) {
                $fornecedoresLog .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$fornecedor->id}, Nome: {$fornecedor->nome}\n";
            }

            
            $produto = Produto::find($request->id_delete);
            $tipo_nome = Tipo_Produto::where('id', $produto->id_tipo)->first()->nome;
            $unidade_medida_nome = Unidade_Medida::where('id', $produto->id_unidade_medida)->first()->nome;
            $produto->delete();

            $log = new Log();
            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Produto deletado:\n" .
                "- ID do Produto: {$produto->id}\n" .
                "- Nome: {$produto->nome}\n" .
                "- Descrição: {$produto->descricao}\n" .
                "- Tipo: ID: {$produto->id_tipo}, Nome: {$tipo_nome}\n" .
                "- Qtd. Aceitável: {$produto->qtd_aceitavel}\n" .
                "- Qtd. Mínima: {$produto->qtd_minima}\n" . 
                "- Fabricantes: " . ($fabricantesLog === "" ? "(não informado)\n" : "\n".$fabricantesLog) .
                "- Fornecedores: " . ($fornecedoresLog === "" ? "(não informado)\n" : "\n".$fornecedoresLog) .
                "- Quarentena: {$produto->quarentena}\n" .
                "- Unidade de Medida: ID: {$produto->id_unidade_medida}, Nome: {$unidade_medida_nome}\n";
            
                $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Produto excluído com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Produto, pois outras informações dependem dele.' . $exception->getMessage())->withInput();
        } 
    }

    public function view_mov(Request $request){
        $lotes_entrada = DB::select('SELECT ID, QTD_ITENS_RECEBIDOS, DATA_ENTREGA FROM PRODUTOS_MOV_IN WHERE ID_PRODUTO = ? ORDER BY ID', [$request->id_view]);
        $lotes_entrada = json_decode(json_encode($lotes_entrada), true);
        
        $lotes_saida = [];
        foreach ($lotes_entrada as $i => $lote_entrada) {
            $lotes_saida[$i] = DB::select('SELECT M.QTD_ITENS_MOVIDOS, M.DATA_MOV_OUT, D.NOME FROM PRODUTOS_MOV_OUT M INNER JOIN DEST_PRODUTOS D ON (M.ID_DESTINO = D.ID) WHERE ID_PRODUTOS_MOV_IN = ? ORDER BY DATA_MOV_OUT', [$lote_entrada['id']]);
            $lotes_saida[$i] = json_decode(json_encode($lotes_saida[$i]), true);
        }

        if ($lotes_entrada == []){
            $view_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal'],
                'alert-dark' => 'Não há movimentação desse produto.'
            ]);
        }
        else{
            $view_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal', '#viewMovModal'],
                'lotes_saida' => $lotes_saida,
                'lotes_entrada' => $lotes_entrada
            ]);
        }

        return redirect()->back()->with($view_movInfos)->withInput();
    }

    public function make_mov(Request $request){
        $make_mov_infos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#makeMovModal'],
        ]);

        return redirect()->back()->with($make_mov_infos)->withInput();
    }

    public function mov_in(Request $request){
        $fabricantes = DB::select('SELECT * FROM FABRICANTES WHERE ID IN (SELECT ID_FABRICANTE FROM PRODUTOS_FAB WHERE ID_PRODUTO = ?)', [$request->id_view]);

        $fornecedores = DB::select('SELECT * FROM FORNECEDORES WHERE ID IN (SELECT ID_FORNECEDOR FROM PRODUTOS_FORN WHERE ID_PRODUTO = ?)', [$request->id_view]);

        $fabricantes = json_decode(json_encode($fabricantes), true);
        $fornecedores = json_decode(json_encode($fornecedores), true);

        $make_mov_inInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#movInModal'],
            'fornecedores_lote' => $fornecedores,
            'fabricantes_lote' => $fabricantes
        ]);

        return redirect()->back()->with($make_mov_inInfos)->withInput();
    }

    public function store_mov_in(Request $request){  
        
        try{
            DB::beginTransaction();

            // ADICIONANDO LOTE DO PRODUTO
            $lote = new Produto_Mov_In;

            $lote->id_produto = $request->id_view;
            $lote->id_fabricante = Fabricante::where('nome', $request->fabricante)->get()[0]->id;
            $lote->lote_fabricante = $request->lote_fabricante;
            $lote->id_fornecedor = Fornecedor::where('nome', $request->fornecedor)->get()[0]->id;
            $lote->qtd_itens_recebidos = $request->qtd_itens_recebidos;
            $lote->qtd_itens_estoque = $request->qtd_itens_recebidos;
            $lote->preco = $request->preco;
            $lote->data_entrega = $request->data_entrega;
            $lote->data_validade = $request->data_validade;
            $lote->quarentena = Produto::where('id', $lote->id_produto)->get()[0]->quarentena;
            
            $lote->save();

            $nome_produto = Produto::findOrFail($lote->id_produto)->nome;

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Movimentação (Entrada) de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Movimentação (Entrada) de Produto adicionada:\n" .
                "- ID da Movimentação (Entrada): {$lote->id}\n" .
                "- Produto: ID: {$lote->id_produto}, nome: {$nome_produto}\n" .
                "- Fabricante: ID: {$lote->id_fabricante}, nome: {$request->fabricante}\n" .
                "- Lote do Fabricante: {$lote->lote_fabricante}\n" .
                "- Fornecedor: ID: {$lote->id_fornecedor}, nome: {$request->fornecedor}\n" .
                "- Qtd. de Itens Recebidos: {$lote->qtd_itens_recebidos}\n" .
                "- Data de Entrega: {$lote->data_entrega}\n" . 
                "- Data de Validade: {$lote->data_validade}\n" .
                "- Quarentena: {$lote->quarentena}\n"; 

            $log->save();

            $store_loteInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal'],
                'id_view_backup' => $request->id_view,
                'alert-success' => 'Movimentaçao de Entrada realizada com Sucesso'
            ]);

            DB::commit();
            return redirect()->back()->with($store_loteInfos);
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

    public function mov_out_select(Request $request){
        $now = Carbon::createFromFormat("H:i:s", date('H:i:s'));
        $lotes = DB::select('SELECT L.ID, F.NOME, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, L.DATA_VALIDADE FROM PRODUTOS_MOV_IN L INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) WHERE L.ID_PRODUTO = ? AND L.QTD_ITENS_ESTOQUE > 0 AND L.DATA_VALIDADE > ? AND L.QUARENTENA = ? ORDER BY L.DATA_VALIDADE ASC', [$request->id_view, $now, 'Não']);
        $lotes = json_decode(json_encode($lotes), true);

        if ($lotes == []){
            $make_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal'],
                'alert-dark' => 'Não há nenhum lote para realizar retirada.'
            ]);
        }
        else{
            $make_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal', '#movOutSelectModal'],
                'lotesP' => $lotes,
                'title_modal' => 'Selecione o lote para retirada:',
                'route_modal' => '.mov_out'
    
            ]);
        }


        return redirect()->back()->with($make_movInfos)->withInput();
    }

    public function mov_out(Request $request){
        $dest_produtos = Dest_Produto::where('nome', '!=', 'VENCIDO')->get();

        $register_movInfos = array_merge(get_infos_view($request), [
            'modal' => ['#viewModal', '#movOutModal'],
            'dest_produtos' => $dest_produtos,
            'qtd_estoque_lote' => $request->qtd_estoque_lote,
            'id_lote' => $request->id_lote
        ]);

        return redirect()->back()->with($register_movInfos)->withInput();
    }

    public function store_mov_out(Request $request){
        try{
            DB::beginTransaction();

            $mov = new Produto_Mov_Out;
    
            $mov->id_produtos_mov_in = $request->id_lote;
            $mov->id_destino = $request->destino;
            $mov->qtd_itens_movidos = $request->qtd_itens_movidos;
            $mov->data_mov_out = $request->data_mov_out;

            $mov->save();

            Produto_Mov_In::findOrFail($request->id_lote)->decrement('qtd_itens_estoque', $request->qtd_itens_movidos);

            $produto = DB::select('SELECT P.ID, P.NOME FROM PRODUTOS P WHERE P.ID = (SELECT L.ID_PRODUTO FROM PRODUTOS_MOV_IN L WHERE L.ID = ?)', [$mov->id_produtos_mov_in])[0];
            $produto = json_decode(json_encode($produto), true);
            
            $nome_destino = Dest_Produto::findOrFail($mov->id_destino)->nome;

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Movimentação (Saída) de Produto')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Movimentação (Entrada) de Produto adicionada:\n" .
                "- ID da Movimentação (Saída): {$mov->id}\n" .
                "- Produto: ID: {$produto['id']}, nome: {$produto['nome']}\n" .
                "- ID da Movimentação (Entrada): {$mov->id_produtos_mov_in}\n" .
                "- Destino: ID: {$mov->id_destino}, nome: {$nome_destino}\n" .
                "- Qtd. de Itens Movidos: {$mov->qtd_itens_movidos}\n" .
                "- Data da Movimentação de Saída: {$mov->data_mov_out}\n"; 

            $log->save();
            
            $store_movInfos = array_merge(get_infos_view($request), [
                'modal' => ['#viewModal'],
                'id_view_backup' => $request->id_view,
                'alert-success' => 'Retirada realizada com sucesso com sucesso'
            ]);
            
            DB::commit();
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

    public function view_expired(){
        $now = Carbon::createFromFormat("H:i:s", date('H:i:s'));

        $lotes_vencidos = DB::select('SELECT L.ID, P.NOME AS PRODUTO, F.NOME AS FABRICANTE, L.LOTE_FABRICANTE, L.QTD_ITENS_ESTOQUE, l.DATA_VALIDADE  FROM PRODUTOS P INNER JOIN PRODUTOS_MOV_IN L ON (P.ID = L.ID_PRODUTO) INNER JOIN FABRICANTES F ON (F.ID = L.ID_FABRICANTE) WHERE L.DATA_VALIDADE < ? AND L.QTD_ITENS_ESTOQUE > 0', [$now]);
        $lotes_vencidos = json_decode(json_encode($lotes_vencidos), true);
        
        if ($lotes_vencidos == [])
            return redirect()->back()->with('alert-dark', 'Não há produtos vencidos no estoque!');

        return redirect()->back()->with(['lotes_vencidos' => $lotes_vencidos, 'modal' => ['#viewExpModal']])->withInput();
    }

    public function destroy_expired(Request $request){
        try{
            DB::beginTransaction();

            $lote = Produto_Mov_In::findOrFail($request->id_exp);


            $mov = new Produto_Mov_Out;
    
            $mov->id_produtos_mov_in = $lote->id;
            $mov->id_destino = Dest_Produto::where('nome', 'VENCIDO')->get()[0]->id;


            $mov->qtd_itens_movidos = $lote->qtd_itens_estoque;
            $mov->data_mov_out = now();

            $mov->save();

            $lote->update(['qtd_itens_estoque' => 0]);

            DB::commit();
            return redirect()->back()->with('alert-success', 'Confirmação de Retirada realizada com sucesso!');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }
}
