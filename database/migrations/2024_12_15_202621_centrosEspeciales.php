<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path=database/migrations/2024_12_15_202621_centrosEspeciales.php
     * no seeder related
     */
    public function up(): void
    {
        Schema::table('centro_costos', function (Blueprint $table) {
            $table->string('ubicacion')->nullable();
            $table->string('corx')->nullable();
            $table->string('cordy')->nullable();
            $table->boolean('valido')->nullable();
            $table->bigInteger('userid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('created_at', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{    }
};
