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
            ['username' => 'admin', 'password' => bcrypt('admin'), 'name' => 'admin', 'cpf' => '000.000.000-00', 'email' => 'admin@admin.com', 'phone' => '(00) 00000-0000']
        );

        DB::table('users_classes')->insert(
            ['id_user' => '1', 'id_classe' => '1']
        );

        DB::table('dest_produtos')->insert(
            ['nome' => 'VENCIDO']
        );
    }
}
