<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCentroCostosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centro_costos', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {

            $table->unsignedBigInteger('centro_costo_id')->nullable();
            $table->foreign('centro_costo_id')
            ->references('id')
            ->on('centro_costos')
            ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('centro_costos');
    }
}
