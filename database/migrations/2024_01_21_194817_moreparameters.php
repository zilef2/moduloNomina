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
    //aun no se introduce = 23enero
    public function up(){
        Schema::table('parametros', function (Blueprint $table) {
            $table->integer('HORAS_DEL_MES_30_DIAS'); //235 = 30 *7.83333
            $table->integer('MAXIMO_HORAS_SEMANALES'); //48
        });
    }


    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
    }
};
