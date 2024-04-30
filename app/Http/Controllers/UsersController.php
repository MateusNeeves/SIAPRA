<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(){
        $usuarios = User::select('id', 'username', 'name')->where('username', '!=', 'admin')->get();
        return view('usuarios/visualizar', ['usuarios' => $usuarios]);
    }

    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['username' => 'unique:users'],
            
            ['username.unique' => 'Já existe um Usuário com esse Username']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#newModal')->withInput();     
    
        $usuario = new User;

        $usuario->username = $request->username;
        $usuario->name = $request->name;
        $usuario->password = bcrypt($request->password);

        $usuario->save();

        return redirect()->route('usuarios')->with('alert-success', 'Usuário cadastrado com sucesso');
    }

    public function update(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['username' => Rule::unique('users')->ignore($request->id)],
            
            ['username.unique' => 'Já existe um Usuário com esse Username']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     

        User::findOrFail($request->id)->update([
            'username' => $request->username,
            'name' => $request->name,
            'password' => bcrypt($request->password)
        ]);
        return redirect()->route('usuarios')->with('alert-success', 'Usuário editado com sucesso');
    }

    public function destroy(Request $request){
        DB::beginTransaction();

        User::find($request->id)->delete();

        if ($request->soft == 'false'){
            try{
                User::withTrashed()->find($request->id)->forceDelete();
                DB::commit();
                return redirect()->back()->with('alert-success', 'Usuário excluído com sucesso');
            }
            catch(\Exception $exception){
                DB::rollBack();
                return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Usuário, pois outras informações dependem dele. <br><br> Deseja Desativar esse Usuário ao invés de Deletar? <br><br> Você pode restaurá-lo futuramente, caso necessário.')->with('modal', '#deleteModal')->withInput();
            } 
        }

        DB::commit();
        return redirect()->back()->with('alert-success', 'Usuário desativado com sucesso');
    }
}
