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
        if ($request->user)
            $userWhere = "WHERE U.ID = (SELECT ID FROM USER WHERE NAME = {$request->user})";
        if ($request->generalSearch)
            $generalSearchWhere = "WHERE L.DESCRICAO LIKE '%{$request->generalSearch}%'";
        $logs = collect(DB::select('SELECT L.ID AS ID_LOG, A.DESCRICAO AS ACAO, U.ID AS USER_ID, U.NAME AS USER_NAME, U.USERNAME AS USER_USERNAME, L.DATA_HORA AS DATA_HORA, L.DESCRICAO AS DESCRICAO FROM LOGS L INNER JOIN USERS U ON (L.ID_USER = U.ID) INNER JOIN ACOES A ON (L.ID_ACAO = A.ID)'));

        return response()->json($request);
    }
}
