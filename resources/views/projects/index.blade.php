@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('projects.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>
				<th>cliente</th>
				<th>num_modulos</th>
				<th>valor_tentativo</th>
				<th>valor_acordado</th>
				<th>valor_primer_pago</th>
				<th>fecha_primera_reunion</th>
				<th>fecha_primer_pago</th>
				<th>fecha_entrega</th>
				<th>observaciones</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($projects as $project)

				<tr>
					<td>{{ $project->id }}</td>
					<td>{{ $project->nombre }}</td>
					<td>{{ $project->cliente }}</td>
					<td>{{ $project->num_modulos }}</td>
					<td>{{ $project->valor_tentativo }}</td>
					<td>{{ $project->valor_acordado }}</td>
					<td>{{ $project->valor_primer_pago }}</td>
					<td>{{ $project->fecha_primera_reunion }}</td>
					<td>{{ $project->fecha_primer_pago }}</td>
					<td>{{ $project->fecha_entrega }}</td>
					<td>{{ $project->observaciones }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('projects.show', [$project->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('projects.edit', [$project->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['projects.destroy', $project->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
