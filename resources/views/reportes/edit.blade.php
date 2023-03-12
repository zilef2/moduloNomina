@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($reporte, array('route' => array('reportes.update', $reporte->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('fecha_ini', 'Fecha_ini', ['class'=>'form-label']) }}
			{{ Form::string('fecha_ini', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('fecha_fin', 'Fecha_fin', ['class'=>'form-label']) }}
			{{ Form::string('fecha_fin', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('horas_trabajadas', 'Horas_trabajadas', ['class'=>'form-label']) }}
			{{ Form::text('horas_trabajadas', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valido', 'Valido', ['class'=>'form-label']) }}
			{{ Form::string('valido', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('observaciones', 'Observaciones', ['class'=>'form-label']) }}
			{{ Form::textarea('observaciones', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
