<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('classes')->insert([
            ['nome' => 'Admin', 'descricao' => 'Administrador do sistema.'],
            ['nome' => 'Almoxarife', 'descricao' => 'Responsável pelo gerenciamento do Almoxarifado.'],
            ['nome' => 'Visualizador', 'descricao' => 'Permite apenas visualização.'],
            ['nome' => 'Produção', 'descricao' => 'Responsável pela Produção.'],
        ]);

        DB::table('users')->insert(
            ['username' => 'admin', 'password' => bcrypt('admin'), 'name' => 'admin', 'cpf' => '00000000000', 'email' => 'admin@admin.com', 'phone' => '00000000000']
        );

        DB::table('users_classes')->insert(
            ['id_user' => '1', 'id_classe' => '1']
        );

        DB::table('dest_produtos')->insert(
            ['nome' => 'VENCIDO']
        );

        DB::table('acoes')->insert([
            ['descricao' => 'Adicionar Fabricante.'],
            ['descricao' => 'Editar Fabricante.'],
            ['descricao' => 'Deletar Fabricante.'],

            ['descricao' => 'Adicionar Fornecedor.'],
            ['descricao' => 'Editar Fornecedor.'],
            ['descricao' => 'Deletar Fornecedor.'],

            ['descricao' => 'Adicionar Tipo de Produto.'],
            ['descricao' => 'Editar Tipo de Produto.'],
            ['descricao' => 'Deletar Tipo de Produto.'],

            ['descricao' => 'Adicionar Destino de Produto.'],
            ['descricao' => 'Editar Destino de Produto.'],
            ['descricao' => 'Deletar Destino de Produto.'],

            ['descricao' => 'Adicionar Produto.'],
            ['descricao' => 'Editar Produto.'],
            ['descricao' => 'Deletar Produto.'],

            ['descricao' => 'Movimentação (Entrada) de Produto.'],
            ['descricao' => 'Movimentação (Saída) de Produto.'],
        ]);
    }
}
