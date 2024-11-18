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
        Schema::create('produtos_mov_out', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_produtos_mov_in');

            $table->unsignedBigInteger('id_destino');

            $table->integer('qtd_itens_movidos');
            
            $table->date('data_mov_out');

            $table->foreign('id_produtos_mov_in')->references('id')->on('produtos_mov_in');
            $table->foreign('id_destino')->references('id')->on('dest_produtos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_mov_out');
    }
};
