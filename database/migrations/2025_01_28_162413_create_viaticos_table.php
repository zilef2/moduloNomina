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
        Schema::create('viaticos', function (Blueprint $table) {
            $table->id();
            $table->biginteger('gasto')->default(0);
            $table->biginteger('saldo')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('legalizacion')->nullable();
            $table->datetime('fecha_legalizacion')->nullable();
            
            $table->unsignedBigInteger('centro_costo_id')->nullable();
            $table->foreign('centro_costo_id')
                ->references('id')
                ->on('centro_costos')
                ->onDelete('cascade'); //cascade, set null, restrict, no action 

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); //cascade, set null, restrict, no action 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viaticos');
    }
};
