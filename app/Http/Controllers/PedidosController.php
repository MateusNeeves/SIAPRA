<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Pedido_Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PedidosController extends Controller
{
    public function index(){
        $pedidos = collect(DB::select('SELECT P.ID, C.NOME_FANTASIA, U.USERNAME, P.QTD_DOSES, P.DATA_SOLICITACAO, P.DATA_ENTREGA
                                        FROM PEDIDOS P INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) INNER JOIN USERS U ON (P.ID_USUARIO = U.ID)'));
        // ALTERANDO FORMATO DA DATA E HORA
        foreach ($pedidos as $idx => $pedido) {
            $pedidos[$idx]->data_solicitacao = Carbon::createFromFormat('Y-m-d H:i:s',  $pedido->data_solicitacao)->format('d/m/Y H:i');
            $pedidos[$idx]->data_entrega = Carbon::createFromFormat('Y-m-d',  $pedido->data_entrega)->format('d/m/Y');
        }
        $pedidos = json_decode(json_encode($pedidos), true);
        $clientes = Cliente::all();
        return view('pedidos/visualizar', ['pedidos' => $pedidos, 'clientes' => $clientes]);
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $id_cliente = Cliente::where('nome_fantasia', $request->cliente)->get()[0]->id;
        $validator = Validator::make(
            ['id_cliente' => $id_cliente],
            
            ['id_cliente' => Rule::unique('pedidos')->where('id_cliente', $id_cliente)->where('data_entrega', $request->data_entrega)],
            
            ['id_cliente.unique' => 'Já existe um Pedido desse Cliente para esse dia']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput(); 
        
        // SALVANDO PEDIDO
        $pedido = new Pedido;

        $pedido->id_cliente = $id_cliente;
        $pedido->id_usuario = Auth::user()->id;
        $pedido->qtd_doses = $request->qtd_doses;
        $pedido->data_solicitacao = $request->data_solicitacao;
        $pedido->data_entrega = $request->data_entrega;

        $pedido->save();

        return redirect()->back()->with('alert-success', 'Pedido cadastrado com sucesso');
    }

    public function edit(Request $request){

        $pedido = collect(DB::select('SELECT C.NOME_FANTASIA AS CLIENTE, P.QTD_DOSES, P.DATA_SOLICITACAO, P.DATA_ENTREGA FROM PEDIDOS P INNER JOIN CLIENTES C ON (P.ID_CLIENTE = C.ID) WHERE P.ID = ?', [$request->id_edit]))[0];

        return redirect()->back()->with(['pedido' => $pedido, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        if (count(Pedido_Plan::where('id_pedido', $request->id_edit)->get()) > 0)
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para alterar os dados desse Pedido, pois outras informações dependem deles.')->withInput();     

        $id_cliente = Cliente::where('nome_fantasia', $request->cliente)->get()[0]->id;
        $validator = Validator::make(
            ['id_cliente' => $id_cliente],
            
            ['id_cliente' => Rule::unique('pedidos')->where('id_cliente', $id_cliente)->where('data_entrega', $request->data_entrega)->ignore($request->id_edit)],
            
            ['id_cliente.unique' => 'Já existe um Pedido desse Cliente para esse dia']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     

        Pedido::findOrFail($request->id_edit)->update([
            'id_cliente' => $id_cliente,
            'qtd_doses' => $request->qtd_doses,
            'data_solicitacao' => $request->data_solicitacao,
            'data_entrega' => $request->data_entrega
        ]);
        return redirect()->back()->with('alert-success', 'Pedido editado com sucesso');
    }

    public function destroy(Request $request){
        try{
            DB::beginTransaction();
            Pedido::find($request->id_delete)->delete();

            DB::commit();
            return redirect()->back()->with('alert-success', 'Pedido excluído com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Pedido, pois outras informações dependem dele.')->withInput();
        } 
    }
}
