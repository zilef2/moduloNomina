<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cotizacions', function (Blueprint $table) {
            $table->unsignedBigInteger('zona_id')->nullable();
            $table->foreign('zona_id')
                ->references('id')
                ->on('zonas')
                ->onDelete('set null'); //cascade, set null, restrict, no action 
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
