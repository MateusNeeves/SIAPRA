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
        Schema::create('pedidos_plan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pedido')->unique();
            $table->unsignedBigInteger('id_planejamento');
            $table->float('ativ_dest');
            $table->float('vol_frasco');

            $table->foreign('id_pedido')->references('id')->on('pedidos');
            $table->foreign('id_planejamento')->references('id')->on('planejamentos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_plan');
    }
};
