<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
			$table->double('subsidio_de_transporte');
			$table->double('salario_minimo');
			$table->double('porcentaje_diurno');
			$table->double('porcentaje_nocturno');
			$table->double('porcentaje_extra_diurno');
			$table->double('porcentaje_extra_nocturno');
			$table->double('porcentaje_dominical_diurno');
			$table->double('porcentaje_dominical_nocturno');
			$table->double('porcentaje_dominical_extra_diurno');
			$table->double('porcentaje_dominical_extra_nocturno');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametros');
    }
}
