<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
php artisan migrate:rollback --path=database/migrations/2025_02_28_160831_create_pagodesarrollos_table.php
php artisan migrate --path=database/migrations/2025_02_28_160831_create_pagodesarrollos_table.php

     */
    public function up(): void {
        Schema::create('pagodesarrollos', function (Blueprint $table) {
            $table->id();
            $table->integer('valor');
            $table->date('fecha');
            $table->integer('cuota');
            $table->integer('final');
            $table->timestamps();

            $table->unsignedBigInteger('desarrollo_id');
            $table->foreign('desarrollo_id')
                ->references('id')
                ->on('desarrollos')
                ->onDelete('cascade'); //cascade, set null, restrict, no action 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('pagodesarrollos');
    }
};
