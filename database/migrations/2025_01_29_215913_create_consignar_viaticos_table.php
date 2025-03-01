<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     php artisan migrate --path=database/migrations/2025_01_29_215913_create_consignar_viaticos_table.php
     * 
     */
    public function up(): void
    {
        Schema::create('consignar_viaticos', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->biginteger('valor_consig');
            $table->date('fecha_consig');

            $table->unsignedBigInteger('user_id')->default(1);//todo:no que le pasa
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); //cascade, set null, restrict, no action 

            $table->unsignedBigInteger('viatico_id')->default(1);//todo:no que le pasa
            $table->foreign('viatico_id')
                ->references('id')
                ->on('viaticos')
                ->onDelete('cascade'); //cascade, set null, restrict, no action 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignar_viaticos');
    }
};
