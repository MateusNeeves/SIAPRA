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
            ['nome' => 'Farmacêutico', 'descricao' => 'Responsável pelo Garantia de Qualidade.'],
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
        
        // ALMOXARIFADO
            ['descricao' => 'Adicionar Fabricante'],
            ['descricao' => 'Editar Fabricante'],
            ['descricao' => 'Deletar Fabricante'],

            ['descricao' => 'Adicionar Fornecedor'],
            ['descricao' => 'Editar Fornecedor'],
            ['descricao' => 'Deletar Fornecedor'],

            ['descricao' => 'Adicionar Tipo de Produto'],
            ['descricao' => 'Editar Tipo de Produto'],
            ['descricao' => 'Deletar Tipo de Produto'],

            ['descricao' => 'Adicionar Unidade de Medida'],
            ['descricao' => 'Editar Unidade de Medida'],
            ['descricao' => 'Deletar Unidade de Medida'],

            ['descricao' => 'Adicionar Destino de Produto'],
            ['descricao' => 'Editar Destino de Produto'],
            ['descricao' => 'Deletar Destino de Produto'],

            ['descricao' => 'Adicionar Produto'],
            ['descricao' => 'Editar Produto'],
            ['descricao' => 'Deletar Produto'],

            ['descricao' => 'Movimentação (Entrada) de Produto'],
            ['descricao' => 'Movimentação (Saída) de Produto'],

            ['descricao' => 'Retirada de Lote da Quarentena'],

        // CLIENTE
            ['descricao' => 'Adicionar Cliente'],
            ['descricao' => 'Editar Cliente'],
            ['descricao' => 'Deletar Cliente'],

        //PEDIDO
            ['descricao' => 'Adicionar Pedido'],
            ['descricao' => 'Editar Pedido'],
            ['descricao' => 'Deletar Pedido'],

        // USUÁRIO
            ['descricao' => 'Adicionar Usuário'],
            ['descricao' => 'Editar Usuário'],
            ['descricao' => 'Deletar Usuário'],

        // PARAMETROS
            ['descricao' => 'Editar Parâmetros'],

        // PLANEJAMENTO
            ['descricao' => 'Adicionar Planejamento'],
            ['descricao' => 'Deletar Planejamento'],

        // FRACIONAMENTO
            ['descricao' => 'Adicionar Fracionamento'],
        ]);

        DB::table('parametros')->insert(
            ['ativ_dose' => 10, 'tempo_exames' => 60, 'vol_max_cq' => 6, 'tempo_exped' => 25, 'rend_tip_ciclotron' => 210, 'corrente_alvo' => 30, 'rend_sintese' => 55, 'tempo_sintese' => 30, 'vol_eos' => 30, 'hora_saida' => '09:00:00']
        );
    }
}
