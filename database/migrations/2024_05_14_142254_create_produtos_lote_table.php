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
        Schema::create('produtos_lote', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_produto');
            $table->unsignedBigInteger('id_fabricante');
            $table->string('lote_fabricante');
            $table->unsignedBigInteger('id_fornecedor');

            $table->integer('qtd_itens_recebidos');
            $table->integer('qtd_itens_estoque');
            
            // $table->integer('preco_unitario');
            // $table->integer('preco_total');

            $table->decimal('preco', 9, 2);
            
            $table->date('data_entrega');
            $table->date('data_validade');

            $table->timestamps();

            $table->foreign('id_produto')->references('id')->on('produtos');
            $table->foreign('id_fabricante')->references('id')->on('fabricantes');
            $table->foreign('id_fornecedor')->references('id')->on('fornecedores');
            $table->unique(['id_produto', 'id_fabricante', 'lote_fabricante']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_lote');
    }
};
