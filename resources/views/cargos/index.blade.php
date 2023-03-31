@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('cargos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($cargos as $cargo)

				<tr>
					<td>{{ $cargo->id }}</td>
					<td>{{ $cargo->nombre }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('cargos.show', [$cargo->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('cargos.edit', [$cargo->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['cargos.destroy', $cargo->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
