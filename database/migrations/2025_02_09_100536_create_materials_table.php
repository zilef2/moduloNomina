<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('materials', function (Blueprint $table) {
            $table->string('nombre', 512);
            $table->text('unidad_de_medida');
            $table->integer('cantidad');
            $table->integer('precio_unitario');
            $table->integer('fecha_adquisicion');
            $table->text('miniatura');
            $table->integer('stock_minimo');
            $table->text('ubicacion');
            $table->id();
            $table->timestamps();
        });

        $generic = 'material';
        Permission::firstOrCreate(['name' => 'delete '.$generic],['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update '.$generic],['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'read '.$generic],['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create '.$generic],['guard_name' => 'web']);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('materials');
    }
};
