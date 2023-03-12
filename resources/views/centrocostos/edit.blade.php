@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($centrocosto, array('route' => array('centrocostos.update', $centrocosto->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('string', 'String', ['class'=>'form-label']) }}
			{{ Form::string('string', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
