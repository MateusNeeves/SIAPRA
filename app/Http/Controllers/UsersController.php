<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Acao;
use App\Models\User;
use App\Models\Classe;
use App\Models\User_Classe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

            $classesLog = "\n";

            // ADICIONANDO CLASSES
            foreach ( $request->classes as $i => $classe) {
                $id_classe = Classe::where('nome', $classe)->first()->id;
                    
                $user_classe[$i] = new User_Classe();
                
                $user_classe[$i]->id_user = $usuario->id;
                $user_classe[$i]->id_classe = $id_classe;
                
                $user_classe[$i]->save();

                $classesLog .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$id_classe}, Nome: {$classe }\n";
            }

            // ADICIONANDO LOG
            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Adicionar Usuário')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Usuário adicionado:\n" .
                "- ID do Usuário: {$usuario->id}\n" .
                "- Username: {$usuario->username}\n" .
                "- Nome: {$usuario->name}\n" .
                "- CPF: {$usuario->cpf}\n" .
                "- Email: {$usuario->email}\n" .
                "- Telefone: {$usuario->phone}\n" .
                "- Classes: {$classesLog}\n";

            $log->save();

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

            $userAntes = $user->toArray();

            $user->update([
                'username' => mb_strtolower($request->username),
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'cpf' => $request->cpf,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $userDepois = $user->refresh()->toArray();

            // ATUALIZANDO CLASSES
            
            // lista desatualizada de classes
            $classes = DB::select('SELECT * FROM CLASSES WHERE ID IN (SELECT ID_CLASSE FROM USERS_CLASSES WHERE ID_USER = ?)', [$user->id]);
            foreach ($classes as $classe)
                $old_classes[] = $classe->nome;
        
            $new_classes_names = array_diff($request->classes ?? [], $old_classes ?? []);
            $removed_classes_names = array_diff($old_classes ?? [], $request->classes ?? []);

            $new_classes = Classe::whereIn('classes.nome', $new_classes_names)->get();
            $removed_classes = Classe::whereIn('classes.nome', $removed_classes_names)->get();
            
        
            // REMOVENDO CLASSES
            User_Classe::join('classes', 'users_classes.id_classe', '=', 'classes.id')->where('users_classes.id_user', $user->id)->whereIn('classes.nome', $removed_classes_names)->delete();

            // ADICIONANDO CLASSES
            foreach ($new_classes as $i => $classe) {                  
                $user_classe[$i] = new User_Classe();
                
                $user_classe[$i]->id_user = $user->id;
                $user_classe[$i]->id_classe = $classe->id;
                
                $user_classe[$i]->save();
            }
            // ADICIONANDO LOG
            if (array_diff_assoc($userAntes, $userDepois) != [] || $new_classes_names != [] || $removed_classes_names != []) {
                $log = new Log();

                $log->id_user = Auth::user()->id;
                $log->id_acao = Acao::where('descricao', 'Editar Usuário')->first()["id"];
                $log->tipo = "Info";
                $log->data_hora = now();
                $log->descricao = 
                    "Usuário editado:\n" .
                    "- ID do Usuário: {$userAntes['id']}\n" .
                    "- Username: {$userAntes['username']}\n\n" .
                    "Campos alterados:\n";

                    foreach ($userDepois as $campo => $valor) {
                        if ($valor != ($userAntes[$campo] ?? null)) {
                            $log->descricao .= "- {$campo}: " .
                                ($userAntes[$campo] === null || $userAntes[$campo] === '' ? '(não informado)' : $userAntes[$campo]) . 
                                " -> " . 
                                ($valor === null || $valor === '' ? '(não informado)' : $valor) . "\n";
                        }
                    }
                
                    // Classes Adicionadas
                    if (!empty($new_classes_names)) {
                        $log->descricao .= "- Classes Adicionadas:\n";
                        foreach ($new_classes as $new_classe) {
                            $log->descricao .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$new_classe->id}, Nome: {$new_classe->nome}\n";
                        }
                    }

                    // Classes Removidas
                    if (!empty($removed_classes_names)) {
                        $log->descricao .= "- Classes Removidas:\n";
                        foreach ($removed_classes as $removed_classe) {
                            $log->descricao .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$removed_classe->id}, Nome: {$removed_classe->nome}\n";
                        }
                    }

                $log->save();
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
        try{
            DB::beginTransaction();

            $classes = DB::select('SELECT ID, NOME FROM CLASSES WHERE ID IN (SELECT ID_CLASSE FROM USERS_CLASSES WHERE ID_USER = ?)', [$request->id_delete]);
            User_Classe::where('id_user', $request->id_delete)->delete();
            
            $classesLog = "\n";
            foreach ($classes as $classe) {
                $classesLog .= "&nbsp;&nbsp;&nbsp;&nbsp;ID: {$classe->id}, Nome: {$classe->nome}\n";
            }
            
            $user = User::find($request->id_delete);
            $user->delete();

            // ADICIONANDO LOG
            $log = new Log();

            $log->id_user = Auth::user()->id;
            $log->id_acao = Acao::where('descricao', 'Deletar Usuário')->first()["id"];
            $log->tipo = "Info";
            $log->data_hora = now();
            $log->descricao = 
                "Usuário deletado:\n" .
                "- ID do Usuário: {$user->id}\n" .
                "- Username: {$user->username}\n" .
                "- Nome: {$user->name}\n" .
                "- CPF: {$user->cpf}\n" .
                "- Email: {$user->email}\n" .
                "- Telefone: {$user->phone}\n" .
                "- Classes: {$classesLog}\n";
            
            $log->save();


            DB::commit();
            return redirect()->back()->with('alert-success', 'Usuário excluído com sucesso');
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('alert-danger', 'Você não tem permissão para excluir esse Usuário, pois outras informações dependem dele.' . $exception->getMessage())->withInput();
        } 
    }
}
