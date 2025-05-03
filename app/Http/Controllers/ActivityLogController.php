<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class ActivityLogController extends Controller {
	
	protected $logFiles = [
		'Superadmin'     => 'Super.log',
		'Administrativo' => 'Administrativo.log',
		'Supervisor'     => 'supervisor.log',
		'Empleados'      => 'laravel.log'
	];
	
	protected $importantActions = [
		'login'    => 'Inició sesión',
		'logout'   => 'Cerró sesión',
		'index'    => 'visito',
		'store'    => 'Creó registro',
		'update'   => 'Actualizó registro',
		'delete'   => 'Eliminó registro',
		'approved' => 'Aprobó solicitud',
		'rejected' => 'Rechazó solicitud',
		'report'   => 'Generó reporte'
	];
	
	public function index(Request $request): \Inertia\Response {
		
		\App\helpers\zzloggingcrud::zilefLogTrace();
		$activities = [];
		$userFilter = $request->input('user');
		$actionFilter = $request->input('action');
		$dateFilter = $request->input('date');
		
		foreach ($this->logFiles as $role => $file) {
			\App\helpers\zzloggingcrud::zilefLogTrace();
			Log::channel('solosuper')->info($role . ' vamos bien');
			
			$filePath = storage_path("logs/{$file}");
			
			if (!File::exists($filePath)) {
				continue;
			}
			
			$lines = $this->tailLines($filePath,100);
			Log::channel('solosuper')->info($role . ' vamos bien con 100 lineas');
			
			foreach ($lines as $line) {
				if (empty($line)) {
					continue;
				}
				
				if (!preg_match('/\[(.*?)\] (\w+)\.(\w+): (.*)/', $line, $matches)) {
					continue;
				}
				
				[$full, $timestamp, $thetype, $logLevel, $message] = $matches;
				$user = $this->extractUser($message, $role);
				$action = $this->extractAction($message);
				$details = $this->extractDetails($message, $action['type']);
				
				if ($this->coninueFilter($userFilter, $actionFilter, $dateFilter, $user, $action, $timestamp)) {
					continue;
				}
				
				$activities[] = [
					'timestamp' => $timestamp,
					'date'      => Carbon::parse($timestamp)->format('d M Y'),
					'date2'     => Carbon::parse($timestamp)->diffForHumans(),
					'time'      => Carbon::parse($timestamp)->format('H:i:s'),
					'user'      => $user,
					'role'      => $role,
					'action'    => $action,
					'details'   => $details,
					'raw'       => $line
				];
			}
		}
		
		usort($activities, fn($a, $b) => strtotime($b['timestamp']) - strtotime($a['timestamp']));
		
		
		return Inertia::render('ActivityLog/Index', [
			'activities'       => $activities,
			'filters'          => $request->only(['user', 'action', 'date']),
			'importantActions' => array_keys($this->importantActions),
			'allUsers'         => collect($activities)->pluck('user')->unique()->values()->all(),
			'methodusers'      => $this->methodusers() ?? [],
		]);
	}
	
	private function tailLines(string $filePath, int $lines = 1000): array {
		$fp = fopen($filePath, 'r');
		if (!$fp) {
			return [];
		}
		
		$buffer = '';
		$lineCount = 0;
		$pos = - 1;
		$result = [];
		
		fseek($fp, 0, SEEK_END);
		$fileSize = ftell($fp);
		
		while ($lineCount < $lines && abs($pos) <= $fileSize) {
			fseek($fp, $pos --, SEEK_END);
			$char = fgetc($fp);
			if ($char === "\n") {
				if ($buffer !== '') {
					$result[] = strrev($buffer);
					$lineCount ++;
					$buffer = '';
				}
			}
			else {
				$buffer .= $char;
			}
		}
		
		if ($buffer !== '') {
			$result[] = strrev($buffer);
		}
		
		fclose($fp);
		
		
		return array_reverse($result); // Más antiguas a más recientes
	}
	
	private function extractUser($message, $defaultRole) {
		// Extraer usuario del mensaje (ej: "U:Superadmin" o "::lejandro222")
		if (preg_match('/U:(\w+)/', $message, $matches)) {
			return $matches[1];
		}
		elseif (preg_match('/::(\w+)/', $message, $matches)) {
			return $matches[1];
		}
		
		
		return $defaultRole;
	}
	
	private function extractAction($message) {
		$PalabrasClave = [
			'Visito'     => 'index',
			'guardo'     => 'store',
			'actualizo'  => 'update',
			'actualizo2' => 'update2',
			'borro'      => 'delete',
			'probo'      => 'test',
		];
		foreach ($PalabrasClave as $human => $action) {
			if (str_contains($message, $action)) {
				return [
					'type'        => $human,
					'description' => 'Visito lista',
					'icon'        => 'list-bullet'
				];
			}
			
		}
		// Identificar acciones importantes con más detalle
		
		//		if (str_contains($message, '|reportes store|')) {
		//			return [
		//				'type'        => 'report',
		//				'description' => 'Creó nuevo reporte',
		//				'icon'        => 'plus'
		//			];
		//		}
		//		
		//		if (str_contains($message, '|reportes update|')) {
		//			return [
		//				'type'        => 'report',
		//				'description' => 'Actualizó reporte',
		//				'icon'        => 'pencil'
		//			];
		//		}
		//		
		//		if (str_contains($message, '|reportes delete|')) {
		//			return [
		//				'type'        => 'report',
		//				'description' => 'Eliminó reporte',
		//				'icon'        => 'trash'
		//			];
		//		}
		
		// Resto de la lógica original...
		foreach ($this->importantActions as $key => $description) {
			if (str_contains($message, "|{$key}|") || str_contains($message, " {$key} ") || str_contains($message, "{$key}::")) {
				return [
					'type'        => $key,
					'description' => $description,
					'icon'        => $this->getActionIcon($key)
				];
			}
		}
		
		
		return [
			'type'        => 'other',
			'description' => 'Acción realizada sin identificar ',
			'icon'        => 'document-text'
		];
	}
	
	private function getActionIcon($action) {
		$icons = [
			'login'    => 'login',
			'logout'   => 'logout',
			'store'    => 'plus',
			'update'   => 'pencil',
			'delete'   => 'trash',
			'approved' => 'check-circle',
			'rejected' => 'x-circle',
			'report'   => 'document-report'
		];
		
		
		return $icons[$action] ?? 'document-text';
	}
	
	private function extractDetails($message, $actionType) {
		$details = [];
		
		// Extraer controlador y método
		if (preg_match('/file = (.*?) line = (\d+) function = (\w+) class = (.*?) type = (.*?)(\s|$)/', $message, $matches)) {
			$details['file'] = $matches[1];
			$details['line'] = $matches[2];
			$details['function'] = $matches[3];
			$details['class'] = $matches[4];
			$details['type'] = $matches[5];
		}
		
		// Extraer información específica para acciones CRUD
		if ($actionType === 'store' || $actionType === 'update' || $actionType === 'delete') {
			if (preg_match('/el type es: (.*?) el id: (\d+)/', $message, $matches)) {
				$details['model_type'] = $matches[1];
				$details['model_id'] = $matches[2];
			}
		}
		
		// Para reportes
		if (str_contains($message, '|reportes ')) {
			$details['report_action'] = str_contains($message, '|reportes index|') ? 'Vió lista de reportes' : (str_contains($message, '|reportes store|') ? 'Creó nuevo reporte' : 'Acción en reportes');
		}
		
		
		return $details;
	}
	
	private function coninueFilter($userFilter, $actionFilter, $dateFilter, $user, $action, $timestamp): bool {
		if ($userFilter && !str_contains(strtolower($user), strtolower($userFilter))) {
			return true;
		}
		if ($actionFilter && $action['type'] !== $actionFilter) {
			return true;
		}
		if ($dateFilter && !str_contains($timestamp, $dateFilter)) {
			return true;
		}
		
		
		return false;
	}
	
	private function methodusers() {
		
		$helperselec = new HelperControllerSelect;
		
		
		//		dd( $helperselec->DependenciasParaVselec('User',' una persona','name'));
		return $helperselec->DependenciasParaVselec('User', ' una persona', 'name');
	}
	
	public function index2(Request $request): \Inertia\Response {
		$activities = [];
		$userFilter = $request->input('user');
		$actionFilter = $request->input('action');
		$dateFilter = $request->input('date');
		
		foreach ($this->logFiles as $role => $file) {
			$filePath = storage_path("logs/{$file}");
			
			if (File::exists($filePath)) {
				$fileContent = File::get($filePath);
				$lines = explode("\n", $fileContent);
				
				foreach ($lines as $line) {
					if (empty($line)) {
						continue;
					}
					
					// Parsear línea de log
					preg_match('/\[(.*?)\] (\w+)\.(\w+): (.*)/', $line, $matches);
					
					if (count($matches) < 5) {
						continue;
					}
					$timestamp = $matches[1];
					$thetype = $matches[2];
					$logLevel = $matches[3];
					$message = $matches[4];
					// Extraer usuario y acción
					$user = $this->extractUser($message, $role);
					$action = $this->extractAction($message);
					$details = $this->extractDetails($message, $action['type']);
					// Filtrar si hay filtros aplicados
					if ($this->coninueFilter($userFilter, $actionFilter, $dateFilter, $user, $action, $timestamp)) {
						continue;
					}
					
					//					dd(
					//					    	'timestamp:: ' . $timestamp,
					//							'date:: '      . Carbon::parse($timestamp)->format('d M Y'),
					//							'time:: '      . Carbon::parse($timestamp)->format('H:i:s'),
					//							'user:: '      . $user,
					//							'role:: '      . $role,
					//							'action:: '    , $action,
					//							'details:: '   , $details, // Nuevo campo con detalles adicionales
					//							'raw:: '       . $line
					//					);
					// Solo registrar acciones importantes
					//					if (array_key_exists($action['type'], $this->importantActions)) {
					$activities[] = [
						'timestamp' => $timestamp,
						'date'      => Carbon::parse($timestamp)->format('d M Y'),
						'date2'     => Carbon::parse($timestamp)->diffForHumans(),
						'time'      => Carbon::parse($timestamp)->format('H:i:s'),
						'user'      => $user,
						'role'      => $role,
						'action'    => $action,
						'details'   => $details, // Nuevo campo con detalles adicionales
						'raw'       => $line
					];
					//					}
				}
			}
		}
		
		// Ordenar por fecha más reciente primero
		usort($activities, function ($a, $b) {
			return strtotime($b['timestamp']) - strtotime($a['timestamp']);
		});
		
		
		return Inertia::render('ActivityLog/Index', [
			'activities'       => $activities,
			'filters'          => $request->only(['user', 'action', 'date']),
			'importantActions' => array_keys($this->importantActions),
			'allUsers'         => collect($activities)->pluck('user')->unique()->values()->all(),
			'methodusers'      => $this->methodusers() ?? [],
			// Nuevo: lista de todos los usuarios
		]);
	}
	
	private function tailLines2(string $filePath, int $lines = 1000): array {
		$output = [];
		$cmd = "tail -n $lines " . escapeshellarg($filePath);
		exec($cmd, $output);
		
		
		return $output;
	}
	
	private function extractAction2_copy($message) {
		// Identificar acciones importantes con más detalle
		if (str_contains($message, '|reportes index|')) {
			return [
				'type'        => 'report',
				'description' => 'Vió lista de reportes',
				'icon'        => 'list-bullet'
			];
		}
		
		if (str_contains($message, '|reportes store|')) {
			return [
				'type'        => 'report',
				'description' => 'Creó nuevo reporte',
				'icon'        => 'plus'
			];
		}
		
		if (str_contains($message, '|reportes update|')) {
			return [
				'type'        => 'report',
				'description' => 'Actualizó reporte',
				'icon'        => 'pencil'
			];
		}
		
		if (str_contains($message, '|reportes delete|')) {
			return [
				'type'        => 'report',
				'description' => 'Eliminó reporte',
				'icon'        => 'trash'
			];
		}
		
		// Resto de la lógica original...
		foreach ($this->importantActions as $key => $description) {
			if (str_contains($message, "|{$key}|") || str_contains($message, " {$key} ") || str_contains($message, "{$key}::")) {
				return [
					'type'        => $key,
					'description' => $description,
					'icon'        => $this->getActionIcon($key)
				];
			}
		}
		
		
		return [
			'type'        => 'other',
			'description' => 'Acción realizada',
			'icon'        => 'document-text'
		];
	}
}