<?php

namespace App\Http\Controllers;

use App\Models\Acao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    public function search(){
        $acoes = Acao::all()->pluck('descricao')->toArray();
        return view('logs/pesquisar', ['acoes' => $acoes]);
    }

    public function view(Request $request){
        // return response()->json($request);
        $allWhere = "WHERE L.DATA_HORA >= '{$request->startDate}' AND L.DATA_HORA < '{$request->endDate}'";

        if ($request->user)
            $allWhere .= " AND U.USERNAME = '{$request->user}'";

        if ($request->generalSearch)
            $allWhere .= " AND L.DESCRICAO ILIKE '%{$request->generalSearch}%'";
        
            $allWhere .= " AND (";
        foreach ($request->acoes as $i => $acao) {
            if ($i > 0) 
                $allWhere .= " OR ";
            $allWhere .= "A.DESCRICAO = '{$acao}'";
        }
        $allWhere .= ")";

        $logs = collect(DB::select('SELECT L.ID AS ID_LOG, A.DESCRICAO AS ACAO, U.ID AS USER_ID, U.NAME AS USER_NAME, U.USERNAME AS USER_USERNAME, L.DATA_HORA AS DATA_HORA, L.DESCRICAO AS DESCRICAO FROM LOGS L INNER JOIN USERS U ON (L.ID_USER = U.ID) INNER JOIN ACOES A ON (L.ID_ACAO = A.ID) ' . $allWhere));

        $logs = json_decode(json_encode($logs), true);
        
        return redirect()->back()->with(['logs' => $logs, 'modal' => ['#viewLogsModal']])->withInput();
    }
}
