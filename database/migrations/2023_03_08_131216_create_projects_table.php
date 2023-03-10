<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('cliente');
			$table->integer('num_modulos');
			$table->integer('valor_tentativo');
			$table->integer('valor_acordado');
			$table->integer('valor_primer_pago');
			$table->date('fecha_primera_reunion');
			$table->datetime('fecha_primer_pago');
			$table->datetime('fecha_entrega');
			$table->text('observaciones');
            $table->timestamps();

            //hasmany cliente
            //hasmany modulos
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
