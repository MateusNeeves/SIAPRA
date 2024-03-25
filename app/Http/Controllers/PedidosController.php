<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pedido;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PedidosController extends Controller
{
    public function index(){
        $pedidos = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, U.USERNAME, P.QTD_DOSES, P.DATA_SOLICITACAO, P.DATA_ENTREGA
                                        FROM PEDIDOS P 
                                        INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) 
                                        INNER JOIN USERS U ON (P.ID_USUARIO = U.ID)'));
        $pedidos = json_decode(json_encode($pedidos), true);
        return view('pedidos/visualizar', ['pedidos' => $pedidos]);
    }

    public function register(){
        $clientes = Cliente::all();
        return view('pedidos/cadastrar', ['clientes' => $clientes]);
    }

    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['id_cliente' => Rule::unique('pedidos')->where('id_cliente', $request->id_cliente)->where('data_entrega', $request->data_entrega)],
            
            ['id_cliente.unique' => 'Já existe um Pedido desse Cliente para esse dia']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput(); 
        
        $pedido = new Pedido;

        $pedido->id_cliente = $request->id_cliente;
        $pedido->id_usuario = Auth::user()->id;
        $pedido->qtd_doses = $request->qtd_doses;
        $pedido->data_solicitacao = $request->data_solicitacao;
        $pedido->data_entrega = $request->data_entrega;

        $pedido->save();

        return redirect('pedidos')->with('alert-success', 'Pedido cadastrado com sucesso');
    }
}
