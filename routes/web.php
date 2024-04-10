<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\FabricantesController;
use App\Http\Controllers\FornecedoresController;
use App\Http\Controllers\PlanejamentosController;
use App\Http\Controllers\TiposProdutosController;
use App\Http\Controllers\FracionamentosController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Início
    Route::get('/dashboard', function(){return view('dashboard');})->middleware(['auth'])->name('dashboard');

// Clientes
    Route::get('/clientes', [ClientesController::class, 'index'])->middleware(['auth'])->name('clientes');
    Route::post('/clientes/cadastrar', [ClientesController::class, 'store'])->middleware(['auth'])->name('clientes.store');
    Route::put('/clientes/editar', [ClientesController::class, 'update'])->middleware(['auth'])->name('clientes.update');
    Route::delete('/clientes/deletar', [ClientesController::class, 'destroy'])->middleware(['auth'])->name('clientes.destroy');
    
//Usuários
    Route::get('/usuarios', [UsersController::class, 'index'])->middleware(['auth'])->name('usuarios');
    Route::post('/usuarios/cadastrar', [UsersController::class, 'store'])->middleware(['auth'])->name('usuarios.store');
    Route::put('/usuarios/editar', [UsersController::class, 'update'])->middleware(['auth'])->name('usuarios.update');
    Route::delete('/usuarios/deletar', [UsersController::class, 'destroy'])->middleware(['auth'])->name('usuarios.destroy');

//Pedidos
    Route::get('/pedidos', [PedidosController::class, 'index'])->middleware(['auth'])->name('pedidos');
    Route::post('/pedidos/cadastrar', [PedidosController::class, 'store'])->middleware(['auth'])->name('pedidos.store');
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


// Fabricantes
    Route::get('/fabricantes', [FabricantesController::class, 'index'])->middleware(['auth'])->name('fabricantes');
    Route::post('/fabricantes/cadastrar', [FabricantesController::class, 'store'])->middleware(['auth'])->name('fabricantes.store');
    Route::put('/fabricantes/editar', [FabricantesController::class, 'update'])->middleware(['auth'])->name('fabricantes.update');
    Route::delete('/fabricantes/deletar', [FabricantesController::class, 'destroy'])->middleware(['auth'])->name('fabricantes.destroy');

// Fornecedores
    Route::get('/fornecedores', [FornecedoresController::class, 'index'])->middleware(['auth'])->name('fornecedores');
    Route::post('/fornecedores/cadastrar', [FornecedoresController::class, 'store'])->middleware(['auth'])->name('fornecedores.store');
    Route::put('/fornecedores/editar', [FornecedoresController::class, 'update'])->middleware(['auth'])->name('fornecedores.update');
    Route::delete('/fornecedores/deletar', [FornecedoresController::class, 'destroy'])->middleware(['auth'])->name('fornecedores.destroy');

// Tipos de Produto
    Route::get('/tipos_produtos', [TiposProdutosController::class, 'index'])->middleware(['auth'])->name('tipos_produtos');
    Route::post('/tipos_produtos/cadastrar', [TiposProdutosController::class, 'store'])->middleware(['auth'])->name('tipos_produtos.store');
    Route::put('/tipos_produtos/editar', [TiposProdutosController::class, 'update'])->middleware(['auth'])->name('tipos_produtos.update');
    Route::delete('/tipos_produtos/deletar', [TiposProdutosController::class, 'destroy'])->middleware(['auth'])->name('tipos_produtos.destroy');

// Produtos
    Route::get('/produtos', [ProdutosController::class, 'index'])->middleware(['auth'])->name('produtos');
    Route::post('/produtos/cadastrar', [ProdutosController::class, 'store'])->middleware(['auth'])->name('produtos.store');
    Route::put('/produtos/editar', [ProdutosController::class, 'update'])->middleware(['auth'])->name('produtos.update');
    Route::delete('/produtos/deletar', [ProdutosController::class, 'destroy'])->middleware(['auth'])->name('produtos.destroy');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
