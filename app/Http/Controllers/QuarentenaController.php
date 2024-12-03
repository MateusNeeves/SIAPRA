<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use Illuminate\Http\Request;
use App\Models\Produto_Mov_In;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuarentenaController extends Controller
{
    public function index(){
        $lotes = DB::select(
           'SELECT L.ID_PRODUTO, P.NOME AS PRODUTO, L.ID AS ID_LOTE, F.NOME AS FABRICANTE, L.LOTE_FABRICANTE, L.DATA_ENTREGA, L.DATA_VALIDADE 
            FROM PRODUTOS_MOV_IN L 
            INNER JOIN FABRICANTES F ON (L.ID_FABRICANTE = F.ID) 
            INNER JOIN PRODUTOS P ON (L.ID_PRODUTO = P.ID) 
            WHERE L.QUARENTENA = ? 
            ORDER BY L.ID_PRODUTO ASC'
        , ['Sim']);
        
        $lotes = json_decode(json_encode($lotes), true);
        return view('quarentena/visualizar', ['lotes' => $lotes]);
    }

    public function remove(Request $request){
        try{
            DB::beginTransaction();

            $lote = Produto_Mov_In::findOrFail($request->id_lote);
            
            $lote->update([
                'quarentena' => 'Não'
            ]);

            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Retirada de Lote da Quarentena')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Lote Retirado da Quarentena:\n" .
                "- ID do Lote: {$request->id_lote}\n";
                
            $log->save();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Lote retirado da Quarentena com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na atualização no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }
}
