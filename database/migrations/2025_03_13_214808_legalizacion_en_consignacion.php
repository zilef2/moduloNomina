<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        if (Schema::hasColumn('consignar_viaticos', 'valor_legalizado')) {
            // Si la columna existe, cambiarla a bigInteger
            Schema::table('consignar_viaticos', function (Blueprint $table) {
                $table->bigInteger('valor_legalizado')->nullable()->change();
            });
        }
        else {
            // Si la columna no existe, crearla
            Schema::table('consignar_viaticos', function (Blueprint $table) {
                $table->bigInteger('valor_legalizado')->nullable();
            });
        }
        
        
        if (Schema::hasColumn('consignar_viaticos', 'fecha_legalizado')) {
            // Si la columna existe, cambiarla a bigInteger
            Schema::table('consignar_viaticos', function (Blueprint $table) {
                $table->dateTime('fecha_legalizado')->nullable()->change();
            });
        }
        else {
            // Si la columna no existe, crearla
            Schema::table('consignar_viaticos', function (Blueprint $table) {
                $table->dateTime('fecha_legalizado')->nullable();
            });
        }
        
        if (Schema::hasColumn('consignar_viaticos', 'descripcion_legalizacion')) {
            // Si la columna existe, cambiarla a bigInteger
            Schema::table('consignar_viaticos', function (Blueprint $table) {
                $table->string('descripcion_legalizacion',512)->nullable()->change();
            });
        }
        else {
            // Si la columna no existe, crearla
            Schema::table('consignar_viaticos', function (Blueprint $table) {
                $table->string('descripcion_legalizacion',512)->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //
    }
};
