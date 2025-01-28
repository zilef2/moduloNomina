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
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('centro_costo_id')->nullable();
            $table->foreign('centro_costo_id')
                ->references('id')
                ->on('centro_costos')
                ->onDelete('cascade'); //cascade, set null, restrict, no action
            $table->unsignedBigInteger('user_id')->default(1);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); //cascade, set null, restrict, no action 
            
            $table->bigInteger('numero_cot');
            $table->string('descripcion_cot');
            $table->integer('precio_cot');
            $table->boolean('aprobado_cot')->default(false);
            $table->date('fecha_aprobacion_cot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacions');
    }
};
