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
        Schema::create('desarrollos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion');
            $table->date('fecha_reunion');
            $table->date('fecha_cotizacion')->nullable();
            $table->date('fecha_cotizacion_aceptada')->nullable();
            $table->string('estado')->nullable();
            
            $table->integer('valor_inicial')->nullable();
            $table->integer('valor_parcial1')->nullable();
            $table->integer('valor_parcial2')->nullable();
            $table->integer('valor_parcial3')->nullable();
            $table->timestamps();
        });
        
        
        Schema::table('centro_costos', function (Blueprint $table) {
            $table->unsignedBigInteger('zona_id')->nullable();
            $table->foreign('zona_id')
                ->references('id')
                ->on('zonas')
                ->onUpdate('cascade') //cascade, set null, restrict, no action 
                ->onDelete('set null'); //cascade, set null, restrict, no action 
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desarrollos');
    }
};
