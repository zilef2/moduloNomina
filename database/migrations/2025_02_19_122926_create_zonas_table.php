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
        Schema::create('zonas', function (Blueprint $table) {
            $table->string('nombre');
            $table->string('nombre2')->nullable();
            $table->string('codigo')->nullable();
            $table->id();
            $table->timestamps();
        });
        
        $generic = 'zona';
        Permission::firstOrCreate(['name' => 'delete '.$generic],['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'update '.$generic],['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'read '.$generic],['guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create '.$generic],['guard_name' => 'web']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
