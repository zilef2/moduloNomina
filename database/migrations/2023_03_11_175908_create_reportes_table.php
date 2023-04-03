<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateReportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
			$table->datetime('fecha_ini');
			$table->datetime('fecha_fin');
			$table->integer('horas_trabajadas')->nullable();
			$table->integer('almuerzo')->nullable();

			$table->integer('diurnas')->nullable();
			$table->integer('nocturnas')->nullable();
			$table->integer('dominicales')->nullable();
			$table->integer('extra_diurnas')->nullable();
			$table->integer('extra_nocturnas')->nullable();
			$table->integer('extra_dominicales')->nullable();

			$table->boolean('valido')->default(0);
			$table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('centro_costo_id');
             
            $table->foreign('centro_costo_id')
                    ->references('id')
                    ->on('centro_costos')
                    ->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
             
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reportes');
    }
}
