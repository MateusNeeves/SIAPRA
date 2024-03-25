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

Route::get('/', function () {
    return redirect()->route('login');
});

// Início
    Route::get('/dashboard', function(){return view('dashboard');})->middleware(['auth', 'verified'])->name('dashboard');

// Clientes
    Route::get('/clientes', [ClientesController::class, 'index'])->middleware(['auth', 'verified'])->name('clientes');
    Route::get('/clientes/cadastrar', [ClientesController::class, 'register'])->middleware(['auth', 'verified'])->name('clientes/cadastrar');
    Route::post('/clientes/cadastrar', [ClientesController::class, 'store'])->middleware(['auth', 'verified'])->name('clientes/cadastrar');

//Usuários
    Route::get('/usuarios', [UsersController::class, 'index'])->middleware(['auth', 'verified'])->name('usuarios');
    Route::get('/usuarios/cadastrar', [UsersController::class, 'register'])->middleware(['auth', 'verified'])->name('usuarios/cadastrar');
    Route::post('/usuarios/cadastrar', [UsersController::class, 'store'])->middleware(['auth', 'verified'])->name('usuarios/cadastrar');

//Pedidos
    Route::get('/pedidos', [PedidosController::class, 'index'])->middleware(['auth', 'verified'])->name('pedidos');
    Route::get('/pedidos/cadastrar', [PedidosController::class, 'register'])->middleware(['auth', 'verified'])->name('pedidos/cadastrar');
    Route::post('/pedidos/cadastrar', [PedidosController::class, 'store'])->middleware(['auth', 'verified'])->name('pedidos/cadastrar');

//Parâmetros
    Route::get('/parametros', [ParametrosController::class, 'index'])->middleware(['auth', 'verified'])->name('parametros');
    Route::get('/parametros/cadastrar', [ParametrosController::class, 'register'])->middleware(['auth', 'verified'])->name('parametros/cadastrar');
    Route::post('/parametros/cadastrar', [ParametrosController::class, 'store'])->middleware(['auth', 'verified'])->name('parametros/cadastrar');

//Planejamento
    Route::get('/planejamentos', [PlanejamentosController::class, 'index'])->middleware(['auth', 'verified'])->name('planejamentos');
    Route::post('/planejamentos', [PlanejamentosController::class, 'show'])->middleware(['auth', 'verified'])->name('planejamentos');
    Route::get('/planejamentos/cadastrar', [PlanejamentosController::class, 'register'])->middleware(['auth', 'verified'])->name('planejamentos/cadastrar');
    Route::post('/planejamentos/cadastrar', [PlanejamentosController::class, 'store'])->middleware(['auth', 'verified'])->name('planejamentos/cadastrar');

    
// Fabricantes
    Route::get('/fabricantes', [FabricantesController::class, 'index'])->middleware(['auth', 'verified'])->name('/fabricantes');
    Route::get('/fabricantes/cadastrar', [FabricantesController::class, 'register'])->middleware(['auth', 'verified'])->name('/fabricantes/cadastrar');
    Route::post('/fabricantes/cadastrar', [FabricantesController::class, 'store'])->middleware(['auth', 'verified'])->name('/fabricantes/cadastrar');

// Fornecedores
    Route::get('/fornecedores', [FornecedoresController::class, 'index'])->middleware(['auth', 'verified'])->name('/fornecedores');
    Route::get('/fornecedores/cadastrar', [FornecedoresController::class, 'register'])->middleware(['auth', 'verified'])->name('/fornecedores/cadastrar');
    Route::post('/fornecedores/cadastrar', [FornecedoresController::class, 'store'])->middleware(['auth', 'verified'])->name('/fornecedores/cadastrar');

// Tipos de Produto
    Route::get('/tipos_produtos', [TiposProdutosController::class, 'index'])->middleware(['auth', 'verified'])->name('/tipos_produtos');
    Route::get('/tipos_produtos/cadastrar', [TiposProdutosController::class, 'register'])->middleware(['auth', 'verified'])->name('/tipos_produtos/cadastrar');
    Route::post('/tipos_produtos/cadastrar', [TiposProdutosController::class, 'store'])->middleware(['auth', 'verified'])->name('/tipos_produtos/cadastrar');

// Produtos
    Route::get('/produtos', [ProdutosController::class, 'index'])->middleware(['auth', 'verified'])->name('/produtos');
    Route::get('/produtos/cadastrar', [ProdutosController::class, 'register'])->middleware(['auth', 'verified'])->name('/produtos/cadastrar');
    Route::post('/produtos/cadastrar', [ProdutosController::class, 'store'])->middleware(['auth', 'verified'])->name('/produtos/cadastrar');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
