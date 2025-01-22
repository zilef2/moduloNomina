<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->double('salario_hora');
			$table->double('salario_total');
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
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('users');
    }
}
