<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::table('viaticos', function (Blueprint $table) {
			
			$table->date('fecha_inicial')->nullable();
			$table->date('fecha_final')->nullable();
			$table->integer('numerodias')->nullable();
			
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		//
	}
};
