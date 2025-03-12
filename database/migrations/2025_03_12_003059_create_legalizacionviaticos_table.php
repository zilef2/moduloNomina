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
        Schema::create('legalizacionviaticos', function (Blueprint $table) {
            $table->integer('valor_legalizacion');
            $table->date('fecha');
            $table->integer('cuota');
            $table->integer('final');
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalizacionviaticos');
    }
};
