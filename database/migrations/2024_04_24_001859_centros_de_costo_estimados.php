<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centro_costos', function (Blueprint $table) {
            $table->double('mano_obra_estimada')->default(0);
            $table->double('mano_obra_real')->default(0);
            $table->integer('activo')->default(1);
            $table->text('descripcion')->nullable();
            //falta
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->string('Nombre');
            //falta
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
