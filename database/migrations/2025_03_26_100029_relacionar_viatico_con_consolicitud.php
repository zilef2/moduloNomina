<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::table('consignar_viaticos', function (Blueprint $table) {
			$table->dropForeign(['viatico_id']);
			$table->dropColumn('viatico_id');
			
			$table->unsignedBigInteger('solicitud_viatico_id')->nullable();
			$table->foreign('solicitud_viatico_id')->references('id')->on('solicitud_viaticos')->onDelete('cascade'); //cascade, set null, restrict, no action 
		});
		
		Schema::table('solicitud_viaticos', function (Blueprint $table) {
			$table->dropForeign(['viatico_id']);
			$table->dropColumn('viatico_id');
			
		});
		
		Schema::table('viaticos', function (Blueprint $table) {
			$table->unsignedBigInteger('solicitud_viatico_id')->nullable();
			$table->foreign('solicitud_viatico_id')->references('id')->on('solicitud_viaticos')->onDelete('cascade'); //cascade, set null, restrict, no action 
		});
		Schema::table('solicitud_viaticos', function (Blueprint $table) {
            $table->biginteger('saldo_sol')->nullable();
			
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void {}
};
