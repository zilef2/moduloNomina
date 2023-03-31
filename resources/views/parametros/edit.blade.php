@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($parametro, array('route' => array('parametros.update', $parametro->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('subsidio_de_transporte', 'Subsidio_de_transporte', ['class'=>'form-label']) }}
			{{ Form::string('subsidio_de_transporte', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('salario_minimo', 'Salario_minimo', ['class'=>'form-label']) }}
			{{ Form::string('salario_minimo', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_diurno', 'Porcentaje_diurno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_diurno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_nocturno', 'Porcentaje_nocturno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_nocturno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_extra_diurno', 'Porcentaje_extra_diurno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_extra_diurno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_extra_nocturno', 'Porcentaje_extra_nocturno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_extra_nocturno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_dominical_diurno', 'Porcentaje_dominical_diurno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_dominical_diurno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_dominical_nocturno', 'Porcentaje_dominical_nocturno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_dominical_nocturno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_dominical_extra_diurno', 'Porcentaje_dominical_extra_diurno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_dominical_extra_diurno', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('porcentaje_dominical_extra_nocturno', 'Porcentaje_dominical_extra_nocturno', ['class'=>'form-label']) }}
			{{ Form::string('porcentaje_dominical_extra_nocturno', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
