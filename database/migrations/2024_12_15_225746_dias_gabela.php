<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     php artisan migrate --path=database/migrations/2024_12_15_225746_dias_gabela.php
     * no seeder related
     */
    public function up(): void
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->integer('s_Dias_gabela')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
