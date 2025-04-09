<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\solicitud_viatico;
use App\Models\viatico;
use Illuminate\Http\Request;

class ViaticosTEst extends TestCase {
	use RefreshDatabase;
	
	public function test_store_creates_solicitud_and_viaticos() {
		// 1. Preparar datos de prueba
//		$this->seed(); // Ejecuta todos los seeders
		  $this->artisan('migrate:fresh --seed');
//		  $this->artisan('db:seed');
        $cargo = \App\Models\Cargo::find(1);
//        dd($cargo);
		$user = User::factory()->create();
        $user->assignRole('empleado');
		
		$this->actingAs($user);
		
		$requestData = [
			'centro_costo_id' => [['id' => 1]],
			'user_id'         => [['id' => $user->id]],
			'descripcion'     => ['Descripción prueba'],
			'gasto'           => [1000],
			'fecha_inicial'   => [['2023-01-01', '2023-01-05']],
			'fecha_final'     => [['2023-01-05']],
			'numerodias'      => [5],
			'Solicitante'     => ['id' => $user->id],
			'Fechasol'        => '2023-01-01',
			'Ciudad'          => 'Ciudad Prueba',
			'ObraServicio'    => 'Obra Prueba'
		];
		
		// 2. Crear request simulada
		$request = new Request($requestData);
		
		// 3. Ejecutar el métdo
		$response = $this->post(route('solicitud_viatico.store'), $requestData);
		
		// 4. Verificaciones
		$this->assertDatabaseHas('solicitud_viaticos', [
			'user_id'   => $user->id,
			'saldo_sol' => 1000
		]);
		
		$this->assertDatabaseHas('viaticos', [
			'gasto'   => 1000,
			'user_id' => $user->id
		]);
		
		$response->assertRedirect()->assertSessionHas('success');
	}
}
