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
        Schema::table('consignar_viaticos', function (Blueprint $table) {
            $table->bigInteger('valor_legalizado')->nullable();
            $table->dateTime('valor_legalizado')->nullable();
            $table->string('descripcion_legalizacion',512)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
