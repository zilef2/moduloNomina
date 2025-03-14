<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('cotizacions', function (Blueprint $table) {
            $table->unsignedBigInteger('persona_que_realiza_la_pe')->nullable();
            $table->foreign('persona_que_realiza_la_pe')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade') 
                ->onDelete('cascade'); //cascade, set null, restrict, no action 
//            $table->unsignedBigInteger('persona_que_solicita_la_propuesta_economica')->nullable();
//            $table->foreign('persona_que_solicita_la_propuesta_economica')
//                ->references('id')
//                ->on('users')
//                ->onDelete('cascade'); //cascade, set null, restrict, no action 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        //
    }
};
