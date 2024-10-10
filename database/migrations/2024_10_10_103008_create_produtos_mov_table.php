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
        Schema::create('produtos_mov', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_produto');
            $table->unsignedBigInteger('id_fabricante');
            $table->unsignedBigInteger('id_fornecedor');

            $table->unsignedBigInteger('id_destino');

            $table->integer('qtd_itens_movidos');
            
            $table->timestamp('hora_mov');

            $table->timestamps();

            $table->foreign('id_produto')->references('id')->on('produtos');
            $table->foreign('id_fabricante')->references('id')->on('fabricantes');
            $table->foreign('id_fornecedor')->references('id')->on('fornecedores');
            $table->foreign('id_destino')->references('id')->on('produtos_dest');
            $table->unique(['id_produto', 'id_fabricante', 'lote_fabricante']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_mov');
    }
};
