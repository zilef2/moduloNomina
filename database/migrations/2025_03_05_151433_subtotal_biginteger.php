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
            $table->decimal('subtotal', 23, 3)->nullable()->change();
            $table->decimal('iva', 23, 3)->nullable()->change();
            $table->decimal('total', 23, 3)->nullable()->change();
            
            $table->dropColumn('persona_que_realiza_la_pe');
            $table->dropColumn('persona_que_solicita_la_propuesta_economica');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizacions', function (Blueprint $table) {
            $table->decimal('subtotal')->nullable()->change();
            $table->decimal('iva')->nullable()->change();
            $table->decimal('total')->nullable()->change();
            
//            $table->dropForeign(['persona_que_realiza_la_pe']);
//            $table->dropColumn('persona_que_realiza_la_pe');
            
//            $table->dropForeign(['persona_que_solicita_la_propuesta_economica']);
//            $table->dropColumn('persona_que_solicita_la_propuesta_economica');
            
            $table->string('persona_que_realiza_la_pe')->nullable();
            $table->string('persona_que_solicita_la_propuesta_economica')->nullable();
            
            
        });
    }
};
