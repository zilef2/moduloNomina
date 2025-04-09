<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory {
	
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() {
		return [
			'name'              => $this->faker->name(),
			'email'             => $this->faker->unique()->safeEmail(),
			'password'          => bcrypt('password'), // password común para testing
			'cedula'            => $this->faker->unique()->numerify('##########'), // 10 dígitos
			'telefono'          => $this->faker->numerify('########'), // 8 dígitos
			'celular'           => $this->faker->numerify('##########'), // 10 dígitos
			'cargo_id'          => \App\Models\Cargo::find(1), // Asume que tienes modelo Cargo
			'fecha_de_ingreso'  => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
			'sexo'              => $this->faker->randomElement(['M', 'F']),
			'salario'           => $this->faker->numberBetween(1000000, 5000000), // Rango salarial
			'centro_costo_id'   => \App\Models\CentroCosto::find(1), // Asume modelo CentroCosto
			'numero_contrato'   => $this->faker->unique()->bothify('CONTRAC-####-??'), // Ej: CON-1234-AB
			'remember_token'    => Str::random(10),
			'email_verified_at' => now(),
		];
	}
	
	/**
	 * Indicate that the model's email address should be unverified.
	 *
	 * @return static
	 */
	public function unverified() {
		return $this->state(fn(array $attributes) => [
			'email_verified_at' => null,
		]);
	}
}
