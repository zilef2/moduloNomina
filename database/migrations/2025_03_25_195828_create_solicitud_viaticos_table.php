<?php

use App\Models\Permission;
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
        Schema::create('solicitud_viaticos', function (Blueprint $table) {
            $table->string('Solicitante',512);
            $table->date('Fechasol');
            $table->string('Ciudad',512);
            $table->string('ObraServicio',512);
            $table->id();
            $table->timestamps();
	        
	        $table->unsignedBigInteger('user_id');
	        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
	        $table->unsignedBigInteger('viatico_id');
	        $table->foreign('viatico_id')->references('id')->on('viaticos')->onDelete('cascade');
	        
			//cascade, set null, restrict, no action 
        });
		
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_viaticos');
    }
};
