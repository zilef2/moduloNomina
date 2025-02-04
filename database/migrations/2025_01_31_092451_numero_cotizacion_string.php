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
            $table->string('numero_cot')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizacions', function (Blueprint $table) {
            $table->biginteger('numero_cot')->change();
        });
    }
};
