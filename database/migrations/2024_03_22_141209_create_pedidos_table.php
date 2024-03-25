<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_usuario');
            $table->integer('qtd_doses');
            $table->timestamp('data_solicitacao');
            $table->date('data_entrega');
    
            $table->foreign('id_cliente')->references('id')->on('clientes');
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->unique(['id_cliente', 'data_entrega']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
