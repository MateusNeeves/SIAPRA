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
        Schema::create('fracionamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->date('data_producao')->unique();
            $table->float('ativ_eob_calc');
            $table->float('ativ_eob_real');
            $table->time('fim_sintese');
            $table->float('ativ_eos_nec');
            $table->float('ativ_eos_real');
            $table->float('vol_eos');
            $table->float('ativ_esp');
            $table->float('rend_sintese_real');

            $table->foreign('id_usuario')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fracionamentos');
    }
};
