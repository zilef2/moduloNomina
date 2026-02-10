<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\CentroCosto;
use App\Models\Parametro;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseTruncation;

class CreateReporteTest extends DuskTestCase
{
    // use DatabaseTruncation; // Uncomment to reset DB after each test

    /**
     * A basic browser test example.
     */
    public function test_create_reporte_submission(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Setup required data
            // Ensure Parametros exist for calculation
            if (Parametro::count() === 0) {
                $this->markTestSkipped('No hay parametros en la base de datos.');
            }

            // Create a valid Centro Costo
            $centro = CentroCosto::factory()->create([
                'activo' => 1,
                'nombre' => 'Centro Test Dusk'
            ]);

            // Create a user
            $user = User::factory()->create([
                'email' => 'dusk_test@example.com',
                'password' => bcrypt('password'),
            ]);

            // 2. Login and visit
            $browser->loginAs($user)
                ->visit('/CreateTest')
                ->assertSee('reportes test') // Verify title

                // 3. Interact with Form
                // Wait for Vue components to mount
                ->pause(1000)

                // Set Dates (using script to be robust against datepicker UI)
                // Note: CreateTest.vue uses v-model form.fecha_ini.
                // We can try typing first.
                ->type('#fecha_ini', '01/01/2026')
                ->keys('#fecha_ini', '{tab}')
                ->pause(500)
                // If the above doesn't trigger v-model update, use script:
                // ->script("document.getElementById('fecha_ini').value = '2026-01-01'");
                // ->script("document.getElementById('fecha_ini').dispatchEvent(new Event('input'))");

                // Select Centro Costo (Vue Select)
                ->click('.vs__dropdown-toggle')
                ->waitFor('.vs__dropdown-menu')
                ->click('.vs__dropdown-option:first-child')

                // 4. Submit
                ->press('CREAR') // Using language key 'button.add' => 'CREAR'

                // 5. Assertions
                ->waitForText('Reporte creado satisfactoriamente') // From controller redirect back with success
                ->assertPathIs('/CreateTest'); // Controller redirects back()

        // Clean up if needed (or rely on DatabaseTruncation)
        });
    }
}
