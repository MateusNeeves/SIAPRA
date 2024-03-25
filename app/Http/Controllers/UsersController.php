<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

        return redirect('usuarios')->with('alert-success', 'Usuário cadastrado com sucesso');
    }
}
