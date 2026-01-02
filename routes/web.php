<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CheckUserClasses;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\QuarentenaController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\FabricantesController;
use App\Http\Controllers\DestProdutosController;
use App\Http\Controllers\FornecedoresController;
use App\Http\Controllers\PlanejamentosController;
use App\Http\Controllers\RegistrosLoteController;
use App\Http\Controllers\TiposProdutosController;
use App\Http\Controllers\FracionamentosController;
use App\Http\Controllers\UnidadesMedidaController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Início
    Route::get('/dashboard', function(){return view('dashboard');})->middleware(['auth'])->name('dashboard');

// Logs
    Route::middleware(CheckUserClasses::class.':Admin,Visualizador,Produção')->group(function () {
        Route::get('/logs', [LogsController::class, 'search'])->middleware(['auth'])->name('logs');
        Route::get('/logs/visualizar', [LogsController::class, 'view'])->middleware(['auth'])->name('logs.view');

    });

    
// Produção
    Route::middleware(CheckUserClasses::class.':Admin,Visualizador,Produção')->group(function () {
        // Clientes
            Route::get('/clientes', [ClientesController::class, 'index'])->middleware(['auth'])->name('clientes');
            Route::get('/clientes/cadastrar', [ClientesController::class, 'register'])->middleware(['auth'])->name('clientes.register');
            Route::post('/clientes/cadastrar', [ClientesController::class, 'store'])->middleware(['auth'])->name('clientes.store');
            Route::post('/clientes/editar', [ClientesController::class, 'edit'])->middleware(['auth'])->name('clientes.edit');
            Route::put('/clientes/editar', [ClientesController::class, 'update'])->middleware(['auth'])->name('clientes.update');
            Route::delete('/clientes/deletar', [ClientesController::class, 'destroy'])->middleware(['auth'])->name('clientes.destroy');
            
        //Usuários
            Route::get('/usuarios', [UsersController::class, 'index'])->middleware(['auth'])->name('usuarios');
            Route::get('/usuarios/cadastrar', [UsersController::class, 'register'])->middleware(['auth'])->name('usuarios.register');
            Route::post('/usuarios/cadastrar', [UsersController::class, 'store'])->middleware(['auth'])->name('usuarios.store');
            Route::post('/usuarios/editar', [UsersController::class, 'edit'])->middleware(['auth'])->name('usuarios.edit');
            Route::put('/usuarios/editar', [UsersController::class, 'update'])->middleware(['auth'])->name('usuarios.update');
            Route::delete('/usuarios/deletar', [UsersController::class, 'destroy'])->middleware(['auth'])->name('usuarios.destroy');
        
        //Pedidos
            Route::get('/pedidos', [PedidosController::class, 'index'])->middleware(['auth'])->name('pedidos');
            Route::get('/pedidos/cadastrar', [PedidosController::class, 'register'])->middleware(['auth'])->name('pedidos.register');
            Route::post('/pedidos/cadastrar', [PedidosController::class, 'store'])->middleware(['auth'])->name('pedidos.store');
            Route::post('/pedidos/editar', [PedidosController::class, 'edit'])->middleware(['auth'])->name('pedidos.edit');
            Route::put('/pedidos/editar', [PedidosController::class, 'update'])->middleware(['auth'])->name('pedidos.update');
            Route::delete('/pedidos/deletar', [PedidosController::class, 'destroy'])->middleware(['auth'])->name('pedidos.destroy');
        
        //Parâmetros
            Route::get('/parametros', [ParametrosController::class, 'index'])->middleware(['auth'])->name('parametros');
            Route::put('/parametros/editar', [ParametrosController::class, 'update'])->middleware(['auth'])->name('parametros.update');
        
        //Planejamentos
            Route::get('/planejamentos', [PlanejamentosController::class, 'index'])->middleware(['auth'])->name('planejamentos');
            Route::post('/planejamentos', [PlanejamentosController::class, 'show'])->middleware(['auth'])->name('planejamentos.show');
            Route::get('/planejamentos/cadastrar', [PlanejamentosController::class, 'register'])->middleware(['auth'])->name('planejamentos.register');
            Route::post('/planejamentos/cadastrar', [PlanejamentosController::class, 'store'])->middleware(['auth'])->name('planejamentos.store');
            Route::delete('/planejamentos/deletar', [PlanejamentosController::class, 'destroy'])->middleware(['auth'])->name('planejamentos.destroy');
        
        //Fracionamentos
            Route::get('/fracionamentos', [FracionamentosController::class, 'index'])->middleware(['auth'])->name('fracionamentos');
            Route::post('/fracionamentos', [FracionamentosController::class, 'show'])->middleware(['auth'])->name('fracionamentos.show');
            Route::get('/fracionamentos/cadastrar', [FracionamentosController::class, 'register'])->middleware(['auth'])->name('fracionamentos.register');
            Route::post('/fracionamentos/cadastrar', [FracionamentosController::class, 'store'])->middleware(['auth'])->name('fracionamentos.store');
        });

    Route::middleware(CheckUserClasses::class.':Admin,Visualizador,Produção,Farmacêutico')->group(function () {
        //Registros de Lote
            Route::get('/registros_lote', [RegistrosLoteController::class, 'index'])->middleware(['auth'])->name('registros_lote');
            Route::get('/registros_lote/pdf', [RegistrosLoteController::class, 'make_pdf'])->middleware(['auth'])->name('registros_lote.make_pdf');
            Route::get('/registros_lote/cadastrar', [RegistrosLoteController::class, 'register'])->middleware(['auth'])->name('registros_lote.register');
            Route::post('/registros_lote/cadastrar', [RegistrosLoteController::class, 'store'])->middleware(['auth'])->name('registros_lote.store');
    });

// Almoxarifado
    Route::middleware(CheckUserClasses::class.':Admin,Almoxarife,Visualizador')->group(function () {
        // Fabricantes
            Route::get('/fabricantes', [FabricantesController::class, 'index'])->middleware(['auth'])->name('fabricantes');
            Route::get('/fabricantes/cadastrar', [FabricantesController::class, 'register'])->middleware(['auth'])->name('fabricantes.register');
            Route::post('/fabricantes/cadastrar', [FabricantesController::class, 'store'])->middleware(['auth'])->name('fabricantes.store');
            Route::post('/fabricantes/editar', [FabricantesController::class, 'edit'])->middleware(['auth'])->name('fabricantes.edit');
            Route::put('/fabricantes/editar', [FabricantesController::class, 'update'])->middleware(['auth'])->name('fabricantes.update');
            Route::delete('/fabricantes/deletar', [FabricantesController::class, 'destroy'])->middleware(['auth'])->name('fabricantes.destroy');
        
        // Fornecedores
            Route::get('/fornecedores', [FornecedoresController::class, 'index'])->middleware(['auth'])->name('fornecedores');
            Route::get('/fornecedores/cadastrar', [FornecedoresController::class, 'register'])->middleware(['auth'])->name('fornecedores.register');
            Route::post('/fornecedores/cadastrar', [FornecedoresController::class, 'store'])->middleware(['auth'])->name('fornecedores.store');
            Route::post('/fornecedores/editar', [FornecedoresController::class, 'edit'])->middleware(['auth'])->name('fornecedores.edit');
            Route::put('/fornecedores/editar', [FornecedoresController::class, 'update'])->middleware(['auth'])->name('fornecedores.update');
            Route::delete('/fornecedores/deletar', [FornecedoresController::class, 'destroy'])->middleware(['auth'])->name('fornecedores.destroy');
        
        // Tipos de Produto
            Route::get('/tipos_produtos', [TiposProdutosController::class, 'index'])->middleware(['auth'])->name('tipos_produtos');
            Route::get('/tipos_produtos/cadastrar', [TiposProdutosController::class, 'register'])->middleware(['auth'])->name('tipos_produtos.register');
            Route::post('/tipos_produtos/cadastrar', [TiposProdutosController::class, 'store'])->middleware(['auth'])->name('tipos_produtos.store');
            Route::post('/tipos_produtos/editar', [TiposProdutosController::class, 'edit'])->middleware(['auth'])->name('tipos_produtos.edit');
            Route::put('/tipos_produtos/editar', [TiposProdutosController::class, 'update'])->middleware(['auth'])->name('tipos_produtos.update');
            Route::delete('/tipos_produtos/deletar', [TiposProdutosController::class, 'destroy'])->middleware(['auth'])->name('tipos_produtos.destroy');  
        
        // Unidades de Medida
            Route::get('/unidades_medida', [UnidadesMedidaController::class, 'index'])->middleware(['auth'])->name('unidades_medida');
            Route::get('/unidades_medida/cadastrar', [UnidadesMedidaController::class, 'register'])->middleware(['auth'])->name('unidades_medida.register');
            Route::post('/unidades_medida/cadastrar', [UnidadesMedidaController::class, 'store'])->middleware(['auth'])->name('unidades_medida.store');
            Route::post('/unidades_medida/editar', [UnidadesMedidaController::class, 'edit'])->middleware(['auth'])->name('unidades_medida.edit');
            Route::put('/unidades_medida/editar', [UnidadesMedidaController::class, 'update'])->middleware(['auth'])->name('unidades_medida.update');
            Route::delete('/unidades_medida/deletar', [UnidadesMedidaController::class, 'destroy'])->middleware(['auth'])->name('unidades_medida.destroy');
        
        // Destino do Produto
            Route::get('/dest_produtos', [DestProdutosController::class, 'index'])->middleware(['auth'])->name('dest_produtos');
            Route::get('/dest_produtos/cadastrar', [DestProdutosController::class, 'register'])->middleware(['auth'])->name('dest_produtos.register');
            Route::post('/dest_produtos/cadastrar', [DestProdutosController::class, 'store'])->middleware(['auth'])->name('dest_produtos.store');
            Route::post('/dest_produtos/editar', [DestProdutosController::class, 'edit'])->middleware(['auth'])->name('dest_produtos.edit');
            Route::put('/dest_produtos/editar', [DestProdutosController::class, 'update'])->middleware(['auth'])->name('dest_produtos.update');
            Route::delete('/dest_produtos/deletar', [DestProdutosController::class, 'destroy'])->middleware(['auth'])->name('dest_produtos.destroy');
        // Produtos
            Route::get('/produtos', [ProdutosController::class, 'index'])->middleware(['auth'])->name('produtos');
            Route::get('/produtos/cadastrar', [ProdutosController::class, 'register'])->middleware(['auth'])->name('produtos.register');
            Route::post('/produtos/cadastrar', [ProdutosController::class, 'store'])->middleware(['auth'])->name('produtos.store');
            Route::post('/produtos/visualizar', [ProdutosController::class, 'view'])->middleware(['auth'])->name('produtos.view');
            Route::post('/produtos/editar', [ProdutosController::class, 'edit'])->middleware(['auth'])->name('produtos.edit');
            Route::put('/produtos/editar', [ProdutosController::class, 'update'])->middleware(['auth'])->name('produtos.update');
            Route::delete('/produtos/deletar', [ProdutosController::class, 'destroy'])->middleware(['auth'])->name('produtos.destroy');
            
            Route::get('/produtos/movimentacao', [ProdutosController::class, 'view_mov'])->middleware(['auth'])->name('produtos.view_mov');
            Route::get('/produtos/movimentar', [ProdutosController::class, 'make_mov'])->middleware(['auth'])->name('produtos.make_mov');
            Route::get('/produtos/movimentar/entrada', [ProdutosController::class, 'mov_in'])->middleware(['auth'])->name('produtos.mov_in');
            Route::post('/produtos/movimentar/entrada', [ProdutosController::class, 'store_mov_in'])->middleware(['auth'])->name('produtos.store_mov_in');
            Route::get('/produtos/movimentar/saida/selecionar_lote', [ProdutosController::class, 'mov_out_select'])->middleware(['auth'])->name('produtos.mov_out_select');
            Route::get('/produtos/movimentar/saida', [ProdutosController::class, 'mov_out'])->middleware(['auth'])->name('produtos.mov_out');
            Route::post('/produtos/movimentar/saida', [ProdutosController::class, 'store_mov_out'])->middleware(['auth'])->name('produtos.store_mov_out');
        
            Route::get('/produtos/vencidos', [ProdutosController::class, 'view_expired'])->middleware(['auth'])->name('produtos.view_expired');
            Route::post('/produtos/vencidos', [ProdutosController::class, 'destroy_expired'])->middleware(['auth'])->name('produtos.destroy_expired'); 

        // Relatórios
            Route::get('/relatorios', [RelatoriosController::class, 'index'])->middleware(['auth'])->name('relatorios');
            Route::get('/relatorios/gerar', [RelatoriosController::class, 'generate'])->middleware(['auth'])->name('relatorios.generate');
        
        });

    Route::middleware(CheckUserClasses::class.':Admin,Visualizador,Farmacêutico')->group(function () {
        // Quarentena
            Route::get('/quarentena', [QuarentenaController::class, 'index'])->middleware(['auth'])->name('quarentena');
            Route::post('/quarentena', [QuarentenaController::class, 'remove'])->middleware(['auth'])->name('quarentena.remove');
    });




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Troca de senha
    Route::get('/perfil/senha', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::post('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});


require __DIR__.'/auth.php';
