@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('centrocostos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>string</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($centrocostos as $centrocosto)

				<tr>
					<td>{{ $centrocosto->id }}</td>
					<td>{{ $centrocosto->string }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('centrocostos.show', [$centrocosto->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('centrocostos.edit', [$centrocosto->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['centrocostos.destroy', $centrocosto->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
