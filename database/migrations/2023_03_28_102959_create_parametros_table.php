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
			$table->float('subsidio_de_transporte');
			$table->float('salario_minimo');
			$table->float('porcentaje_diurno');
			$table->float('porcentaje_nocturno');
			$table->float('porcentaje_extra_diurno');
			$table->float('porcentaje_extra_nocturno');
			$table->float('porcentaje_dominical_diurno');
			$table->float('porcentaje_dominical_nocturno');
			$table->float('porcentaje_dominical_extra_diurno');
			$table->float('porcentaje_dominical_extra_nocturno');
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cargo_id')->default(1);
             
            $table->foreign('cargo_id')
                    ->references('id')
                    ->on('cargos')
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
        Schema::dropIfExists('parametros');
    }
}
