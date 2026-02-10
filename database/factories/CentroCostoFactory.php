<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentroCosto>
 */
class CentroCostoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'mano_obra_estimada' => $this->faker->numberBetween(1000000, 10000000),
            'activo' => 1,
            'descripcion' => $this->faker->sentence(),
            'clasificacion' => $this->faker->word(),
            'ValidoParaFacturar' => $this->faker->boolean(),
            'zona_id' => \App\Models\Zona::factory(),
        ];
    }
}
