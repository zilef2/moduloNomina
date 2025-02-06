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
        Schema::table('viaticos', function (Blueprint $table) {
            
            $table->biginteger('valor_legalizacion')->nullable();
            $table->string('descripcion_legalizacion',512)->nullable();
        });
    }

    public function down(): void {}
};
