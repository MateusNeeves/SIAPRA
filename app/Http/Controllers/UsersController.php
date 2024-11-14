<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\User;
use App\Models\User_Classe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(){
        $usuarios = User::where('username', '!=', 'admin')->get();
        
        foreach ($usuarios as $i => $usuario) {
            $usuarios[$i]->cpf = formatCpf($usuario->cpf);
            $usuarios[$i]->phone = formatPhone($usuario->phone);
            
            $classes = DB::select('SELECT NOME FROM CLASSES WHERE ID IN (SELECT ID_CLASSE FROM USERS_CLASSES WHERE ID_USER = ?)', [$usuario->id]);
            
            $classesStr = "";
            $lastIndex = count($classes) - 1;
            foreach ($classes as $j => $classe) {
                $classesStr .= $classe->nome;
                if ($j !== $lastIndex) {
                    $classesStr .= ", ";
                }
            }

            $usuarios[$i]->classes = $classesStr;
        }

        return view('usuarios/visualizar', ['usuarios' => $usuarios]);
    }

    public function register(){
        $classes = Classe::all();
        return redirect()->back()->with(['modal' => '#newModal', 'classes' => $classes]);
    }

    public function store(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            $request->all(),
            
            ['username' => 'unique:users',
            'cpf' => 'unique:users'],
            
            ['username.unique' => 'Já existe um Usuário com esse Username',
            'cpf.unique' => 'Já existe um Usuário com esse CPF']
        );

        if ($validator->fails()){
            $classes = Classe::all();
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with(['modal' => '#newModal', 'classes' => $classes])->withInput();     
        }

        try{
            DB::beginTransaction();

            // ADICIONANDO USUARIO
            $usuario = new User;

            $usuario->username = mb_strtolower($request->username);
            $usuario->name = $request->name;
            $usuario->password = bcrypt($request->password);
            $usuario->cpf = $request->cpf;
            $usuario->email = $request->email;
            $usuario->phone = $request->phone;

            $usuario->save();

            // ADICIONANDO CLASSES
            foreach ((array) $request->classes as $i => $classe) {
                $id_classe = Classe::where('nome', $classe)->first()->id;
                    
                $user_classe[$i] = new User_Classe();
                
                $user_classe[$i]->id_user = $usuario->id;
                $user_classe[$i]->id_classe = $id_classe;
                
                $user_classe[$i]->save();
            }

            DB::commit();
            return redirect()->route('usuarios')->with('alert-success', 'Usuário cadastrado com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }
    }

    public function edit(Request $request){
        $usuario = User::where('id', $request->id_edit)->get()[0];
        $classes = Classe::all();

        $classesSelected = DB::select('SELECT NOME FROM CLASSES WHERE ID IN (SELECT ID_CLASSE FROM USERS_CLASSES WHERE ID_USER = ?)', [$request->id_edit]);

        foreach ($classesSelected as $classe)
            $classesSelected[] = $classe->nome;
 
        // return response()->json(['usuario' => $usuario, 'classes' => $classes, 'classesSelected' => $classesSelected, 'modal' => '#editModal']);
        return redirect()->back()->with(['usuario' => $usuario, 'classes' => $classes, 'classesSelected' => $classesSelected, 'modal' => '#editModal'])->withInput(); 
    }

    public function update(Request $request){
        // VERIFICANDO UNICIDADE E CONDIÇÕES
        $validator = Validator::make(
            ['username' => mb_strtolower($request->username)],
            
            ['username' => Rule::unique('users')->ignore($request->id_edit),
            'cpf' => Rule::unique('users')->ignore($request->id_edit)],
            
            ['username.unique' => 'Já existe um Usuário com esse Username',
            'cpf.unique' => 'Já existe um Usuário com esse CPF']
        );

        if ($validator->fails())
            return redirect()->back()->with('alert-danger', $validator->messages()->first())->with('modal', '#editModal')->withInput();     
        
        try{
            DB::beginTransaction();

            // SALVANDO TIPO DE PRODUTO
            $user = User::findOrFail($request->id_edit);

            $user->update([
                'username' => mb_strtolower($request->username),
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'cpf' => $request->cpf,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // ATUALIZANDO FORNECEDORES
            
            // lista desatualizada de fornecedores
            $classes = DB::select('SELECT * FROM CLASSES WHERE ID IN (SELECT ID_CLASSE FROM USERS_CLASSES WHERE ID_USER = ?)', [$user->id]);
            foreach ($classes as $classe)
                $old_classes[] = $classe->nome;
        
            $new_classes = array_diff($request->classes ?? [], $old_classes ?? []);
            $removed_classes = array_diff($old_classes ?? [], $request->classes ?? []);
        
            // REMOVENDO CLASSES
            User_Classe::join('classes', 'users_classes.id_classe', '=', 'classes.id')->where('users_classes.id_user', $user->id)->whereIn('classes.nome', $removed_classes)->delete();

            // ADICIONANDO CLASSES
            foreach ($new_classes as $i => $classe) {
                $id_classe = Classe::where('nome', $classe)->first()->id;
                    
                $user_classe[$i] = new User_Classe();
                
                $user_classe[$i]->id_user = $user->id;
                $user_classe[$i]->id_classe = $id_classe;
                
                $user_classe[$i]->save();
            }

            DB::commit();
            return redirect()->route('usuarios')->with('alert-success', 'Usuário editado com sucesso');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return redirect()->back()->with('alert-danger', 'Ocorreu um erro na inserção no banco de dados: ' . $exception->getMessage())->withInput();
        }


    }

    public function destroy(Request $request){
        DB::beginTransaction();

        User::find($request->id_delete)->delete();

        if ($request->soft == 'false'){
            try{
                User::withTrashed()->find($request->id_delete)->forceDelete();
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
