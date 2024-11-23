<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     php artisan migrate --path=database/migrations/2024_11_22_202621_ubicacion.php
     * no seeder related
     */
    public function up(): void
    {
        Schema::create('ubicacion', function (Blueprint $table) {
            $table->string('ubicacion')->nullable();
            $table->string('corx')->nullable();
            $table->string('cordy')->nullable();
            $table->boolean('valido')->nullable();
            $table->bigInteger('userid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{    }
};
