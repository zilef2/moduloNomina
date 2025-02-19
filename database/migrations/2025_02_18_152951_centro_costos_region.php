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
            $table->string('estado_cliente')->nullable();
            $table->string('estado')->nullable();
            $table->string('factura')->nullable();
            $table->date('fecha_factura')->nullable();
            $table->string('mes_pedido')->nullable();
            $table->string('lugar')->nullable();
            $table->string('tipo')->nullable();
            $table->string('tipo_de_mantenimiento')->nullable();
            $table->decimal('por_a')->nullable();
            $table->decimal('por_i')->nullable();
            $table->decimal('por_u')->nullable();
            $table->decimal('admi')->nullable();
            $table->decimal('impr')->nullable();
            $table->decimal('util')->nullable();
            $table->decimal('subtotal')->nullable();
            $table->decimal('iva')->nullable();
            $table->decimal('total')->nullable();
            $table->string('persona_que_realiza_la_pe')->nullable();
            $table->string('cliente')->nullable();
            $table->string('persona_que_solicita_la_propuesta_economica')->nullable();
            $table->string('orden_de_compra')->nullable();
            $table->string('hes')->nullable();
            $table->text('observaciones')->nullable();
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
