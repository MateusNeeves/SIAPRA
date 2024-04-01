<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(){
        $usuarios = User::select('id', 'username', 'name')->get();
        return view('usuarios/visualizar', ['usuarios' => $usuarios]);
    }

    public function register(){
        return view('usuarios/cadastrar');
    }

    public function store(Request $request){
        $validator = Validator::make(
            $request->all(),
            
            ['username' => 'unique:users'],
            
            ['username.unique' => 'Já existe um Usuário com esse Username']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     
    
        $usuario = new User;

        $usuario->username = $request->username;
        $usuario->name = $request->name;
        $usuario->password = bcrypt($request->password);

        $usuario->save();

        return redirect()->route('usuarios')->with('alert-success', 'Usuário cadastrado com sucesso');
    }

    public function edit($id){
            $usuario = User::find($id);
            if ($usuario)
                return view('usuarios/editar', ['usuario' => $usuario]);
            else
                return redirect()->route('usuarios')->with('alert-danger', 'Usuário de id #' . $id . ' não encontrado.');
    }

    public function update(Request $request, $id){
        $validator = Validator::make(
            $request->all(),
            
            ['username' => Rule::unique('users')->ignore($id)],
            
            ['username.unique' => 'Já existe um Usuário com esse Username']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->withInput();     

        User::findOrFail($id)->update([
            'username' => $request->username,
            'name' => $request->name,
            'password' => bcrypt($request->password)
        ]);
        return redirect()->route('usuarios')->with('alert-success', 'Usuário editado com sucesso');
    }

    public function destroy(Request $request){
        try{
            User::findOrFail($request->id)->delete();
    
            return redirect()->route('usuarios')->with('alert-success', 'Usuário exclúido com sucesso');
        }
        catch (\Exception $exception) {
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro ao deletar o usuário: ' . $exception->getMessage())->withInput(); 
        }
    }
}
