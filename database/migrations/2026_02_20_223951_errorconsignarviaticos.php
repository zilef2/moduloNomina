<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignar_viaticos', function (Blueprint $table) {

            // 1️⃣ Eliminar FK actual
            $table->dropForeign(['destinatiario_user_id']);

            // 2️⃣ Crear nueva FK apuntando a user_id
            $table->foreign('destinatiario_user_id')
                  ->references('user_id')
                  ->on('viaticos')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consignar_viaticos', function (Blueprint $table) {

            // Revertir: volver a id
            $table->dropForeign(['destinatiario_user_id']);

            $table->foreign('destinatiario_user_id')
                  ->references('id')
                  ->on('viaticos')
                  ->cascadeOnDelete();
        });
    }
};
