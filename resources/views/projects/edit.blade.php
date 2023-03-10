@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($project, array('route' => array('projects.update', $project->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('nombre', 'Nombre', ['class'=>'form-label']) }}
			{{ Form::text('nombre', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('cliente', 'Cliente', ['class'=>'form-label']) }}
			{{ Form::text('cliente', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('num_modulos', 'Num_modulos', ['class'=>'form-label']) }}
			{{ Form::text('num_modulos', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valor_tentativo', 'Valor_tentativo', ['class'=>'form-label']) }}
			{{ Form::text('valor_tentativo', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valor_acordado', 'Valor_acordado', ['class'=>'form-label']) }}
			{{ Form::text('valor_acordado', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valor_primer_pago', 'Valor_primer_pago', ['class'=>'form-label']) }}
			{{ Form::text('valor_primer_pago', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('fecha_primera_reunion', 'Fecha_primera_reunion', ['class'=>'form-label']) }}
			{{ Form::string('fecha_primera_reunion', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('fecha_primer_pago', 'Fecha_primer_pago', ['class'=>'form-label']) }}
			{{ Form::string('fecha_primer_pago', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('fecha_entrega', 'Fecha_entrega', ['class'=>'form-label']) }}
			{{ Form::string('fecha_entrega', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('observaciones', 'Observaciones', ['class'=>'form-label']) }}
			{{ Form::textarea('observaciones', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
