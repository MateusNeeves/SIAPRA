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
            $table->unsignedBigInteger('id_produtos_lote');

            $table->unsignedBigInteger('id_destino');

            $table->integer('qtd_itens_movidos');
            
            $table->timestamp('hora_mov');

            $table->timestamps();

            $table->foreign('id_produtos_lote')->references('id')->on('produtos_lote');
            $table->foreign('id_destino')->references('id')->on('dest_produtos');
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
