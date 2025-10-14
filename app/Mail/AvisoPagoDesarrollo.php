<?php

namespace App\Mail;

use App\helpers\MyGlobalHelp;
use App\Models\desarrollo;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AvisoPagoDesarrollo extends Mailable {
	
	public $desarrollo;
	public $haceCuanto;
	public $fechaVencimiento;
	public int $cuantosDesarrollosPendientes;
	
	public function __construct(Desarrollo $desarrollo, int $cuantosDesarrollosPendientes) {
		$this->cuantosDesarrollosPendientes = $cuantosDesarrollosPendientes;
		$this->desarrollo = $desarrollo;
		
		// hace cuánto fue aceptada la cotización
		
//		$this->haceCuanto = Carbon::parse($desarrollo->fecha_cotizacion_aceptada)->diffForHumans();
		$this->haceCuanto = MyGlobalHelp::diffCarbonMonthNDays($desarrollo->fecha_cotizacion_aceptada);
		
		$hoy = Carbon::today();
		$month = $hoy->month;
		$year = $hoy->year;
		$quincena = ($hoy->day <= 15) ? 1 : 2;
		$fin = Carbon::createFromFormat('d/m/Y', '1/' . $month . '/' . $year);
		if ($quincena == 1) {
			$fin->addDays(14);//antes era 12
		}
		else {
			$fin->addMonths(1)->addDays(- 1); //antes era -4
			$nombreDiaSemana = (int)($fin->format('d'));
			
			if ($nombreDiaSemana == 31) {
				$fin->addDays(- 1);
			}
		}
		$this->fechaVencimiento = $fin;
	}
	
	public function envelope(): Envelope {
		return new Envelope(subject: 'Aviso de pago pendiente - ' . $this->desarrollo->nombre,);
	}
	
	public function content(): Content {
		return new Content(markdown: 'emails.aviso-pago-desarrollo',);
	}
}