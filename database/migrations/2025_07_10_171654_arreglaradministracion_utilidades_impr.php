<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
    {
        Schema::table('cotizacions', function (Blueprint $table) {
            $table->decimal('admi', 28)->nullable()->change();
            $table->decimal('impr', 28)->nullable()->change();
            $table->decimal('util', 28)->nullable()->change();
            $table->decimal('subtotal', 28, 3)->nullable()->change();
            $table->decimal('iva', 28, 3)->nullable()->change();
            $table->decimal('total', 28, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cotizacions', function (Blueprint $table) {
            $table->decimal('admi', 8, 2)->change();
            $table->decimal('impr', 8, 2)->change();
            $table->decimal('util', 8, 2)->change();
            $table->decimal('subtotal', 8, 2)->change();
            $table->decimal('iva', 8, 2)->change();
            $table->decimal('total', 8, 2)->change();
        });
    }
};
