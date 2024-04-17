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
        Schema::create('pedidos_frac', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pedido');
            $table->unsignedBigInteger('id_fracionamento');
            $table->integer('qtd_doses_selec');
            $table->float('ativ_dest');
            $table->integer('qtd_doses_entregues');
            $table->float('vol_real_frasco');

            $table->foreign('id_pedido')->references('id')->on('pedidos');
            $table->foreign('id_fracionamento')->references('id')->on('fracionamentos');
            $table->unique(['id_pedido', 'id_fracionamento']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_frac');
    }
};
