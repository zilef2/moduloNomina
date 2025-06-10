<?php

namespace Database\Seeders;

use App\Models\peusuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeusuarioSeeder extends Seeder {
	
	/**
	 * Run the database seeds.
	 php artisan migrate:rollback && php artisan migrate && php artisan db:seed --class=PeusuarioSeeder
	 php artisan db:seed --class=PeusuarioSeeder
	 *
	 */
	public function run(): void {
		DB::table('peusuarios')->delete();
		
		Peusuario::create(['nombre_solicitante_PE' => 'Albert Matamoros', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Alejandro Hanz', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Alejandro Montoya', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Anderson Franco', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Andres Loaiza', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Andres Martinez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Andrey', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Angela Meneses', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Angie Orjuela', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Ariel Arturo Ayala', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Carlos Andres Gomez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Carlos David Alzate', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Carlos Vallejo', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Carmen Elisa Restrepo', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'David Riveros', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Diego Montoya', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Diego Quiza', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Diego Sanchez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Duvan Rojas', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Edgar Nisperuza', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Edison Ospina', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Eliana Murillo', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Emerson Hernandez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Evelyn Guerrero', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Federico Alvarez Pareja', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Felipe Cepeda', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Fernando Grosso', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Gabriel Vega', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Gentil Hurtatiz', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'German Cardona', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Giovanni Escobar', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Gladys Fonseca', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Gladys Ortiz', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Gloria Maria Elejalde', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Gonzalo Diaz', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Hernán José Blanco Ochoa', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'James Alvarez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jarol Lopera', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Javier Matallana', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jesus Antonio Puentes', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jhon Jairo Panche', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jonathan Restrepo', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jorge Eusse', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jorge Hoyos', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jorge Sanchez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Jose Alejandro Lopera', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan Carlos Beltran', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan Carlos Bravo', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan Castrillon', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan David Duran', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan Esteban Castrillon', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan Hoyos', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Juan Sebastain Luz Galeano', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Keenereth', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Laura Camila Rodriguez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Laura Vargas', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Liliana Rodriguez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Lina Parra', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Marcela Restrepo', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Marcos Rodriguez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Maria Camila Ramirez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Mariana Ospino Osorio', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Maryori Castro', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Mauricio Salazar', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Miguel Buendia', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Natalia Chaves Rocha', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Nicolas Gallego', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Norbey Rendon', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Octavio Bermúdez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Piedad Arango Bedoya', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Ricardo Vivas', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Rocco', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Roel Salgado', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Sebastian Arrubla', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Sebastian Hernandez', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Sebastian Zuluaga', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Sergio Bañol', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Sneider Mejia', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Stiven Madrigal', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Wilmer Reyes', 'clasificacion' => 'persona']);
		Peusuario::create(['nombre_solicitante_PE' => 'Yhon Dairo Zapata', 'clasificacion' => 'persona']);
		
		Peusuario::create(['nombre_solicitante_PE' => 'AMBIENTE SOLAR', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'AZIMUT ENERGIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'BANCO FALABELLA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'BIMBO DE COLOMBIA SA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'BTG PACTUAL', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'CARDIO INFANTIL', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'CELSIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'COL', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'COLGAS S.A E.S.P', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'CONSTRUHIGIENICA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'CONSTRUCTORA AIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'CORPORACION ANTIOQUIA TROPICAL CLUB', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'COTEL', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'DARWIN ENERGIA S.A.S.', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'Divix S.A', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'Edificio Cerros del Country', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'ENERGY MASTER', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'ENERGY360 SAS', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'ETALZA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'FALABELLA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'FALABELLA ABC', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'FALCON', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'FUPBI', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'GRUPO EMPRESARIAL ALVAREZ Y ROMERO', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'HOME SENTRY', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'HYBRYTEC S.A.S.', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'INFRAESTRUTURA DIGITAL', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'ISABELITA #1', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'ISABELITA #2', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'JORGE  ALBERTO HOYOS', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'LER INGENIERIA ELETTRICA S.A.S', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'MINISTERIO DE MINAS Y ENERGÍA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'MONTACARGAS AMYM S.A.S.', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'OPTIMA ENERGIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'PC MEJIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'PETPACK S.A.S', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'PLASTIC WORKS EMS S.A.S.', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'PRORLECSSA SAS', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'QUIMICA ORION S.A.S', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'REDEBAN MULTICOLOR', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'REDYSEL S.A.S.', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'RESERVA DE LA SIERRA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'SODIMAC COLOMBIA S.A', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'SUNCOLOMBIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'TP COLOMBIA', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'TRANSCABLE', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'UT EFISOLAR', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'WINNER GROUP', 'clasificacion' => 'empresa']);
		Peusuario::create(['nombre_solicitante_PE' => 'ZIKLO SOLAR S.A.S.', 'clasificacion' => 'empresa']);
		
	}
}
