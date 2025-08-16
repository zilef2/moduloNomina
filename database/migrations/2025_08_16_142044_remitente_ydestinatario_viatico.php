<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::statement('TRUNCATE TABLE consignar_viaticos');
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		
		Schema::table('consignar_viaticos', function (Blueprint $table) {
			$table->unsignedBigInteger('remitente_user_id');
			$table->foreign('remitente_user_id')->references('id')->on('users')->onDelete('cascade');
			$table->unsignedBigInteger('destinatiario_user_id');
			$table->foreign('destinatiario_user_id')->references('id')->on('viaticos')->onDelete('cascade');
			$table->dropForeign(['user_id']); // Elimina la FK
			$table->dropColumn('user_id');
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::table('tu_tabla', function (Blueprint $table) {
			$table->unsignedBigInteger('user_id')->nullable();
			$table->dropColumn(['remitente_user_id', 'destinatiario_user_id']);
		});
	}
};
