<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('dest_produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('dest_produtos')->insert(
            ['nome' => 'Vencido']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dest_produtos');
    }
};
