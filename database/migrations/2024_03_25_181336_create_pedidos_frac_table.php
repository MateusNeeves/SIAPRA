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
            $table->unsignedBigInteger('id_pedido')->unique();
            $table->unsignedBigInteger('id_fracionamento');
            $table->float('vol_real_frasco');

            $table->foreign('id_pedido')->references('id')->on('pedidos');
            $table->foreign('id_fracionamento')->references('id')->on('fracionamentos');

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
