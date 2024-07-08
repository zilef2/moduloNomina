<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Run the migrations.
     *
     php artisan migrate --path=/database/migrations/2024_05_09_003355_clasificacion_c_c.php
     no seeder related
     * @return void
     */
    public function up(){
        if (!Schema::hasColumn('centro_costos', 'clasificacion')) {
            Schema::table('centro_costos', function (Blueprint $table) {
                $table->string('clasificacion')->nullable();
                $table->boolean('ValidoParaFacturar')->nullable();
            });
        }

        if (!Schema::hasColumn('total_centro_costos', 'ultimo_dia_mes')) {
            Schema::create('total_centro_costos', function (Blueprint $table) {
                $table->datetime('ultimo_dia_mes')->nullable();

                $table->double('total_diurno')->default(0);
                $table->double('total_nocturno')->default(0);
                $table->double('total_extra_diurno')->default(0);
                $table->double('total_extra_nocturno')->default(0);
                $table->double('total_dominical_diurno')->default(0);
                $table->double('total_dominical_nocturno')->default(0);
                $table->double('total_dominical_extra_diurno')->default(0);
                $table->double('total_dominical_extra_nocturno')->default(0);

                $table->double('acumulado_actual')->default(0);
                $table->double('acumulado_anual')->default(0);
                $table->double('total_absoluto')->default(0);
                $table->unsignedBigInteger('centro_costo_id');
                $table->foreign('centro_costo_id')
                    ->references('id')
                    ->on('centro_costos')
                    ->onDelete('restrict'); //cascade | set null
            });
        }

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
//php artisan migrate --path=/database/migrations/filename.php
//php artisan db:seed --class=SeederClassName
