@extends('layouts.app')

@section('title', ' - Plugins')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Plugins</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addPlugin">Add New Plugin &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>BB Source</th>
									<th>Repo Type</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($plugins as $k => $v)
									<tr>
										<td><a href="/tools/plugins/view/{{ $v->id }}">{{ $v->id }}</a></td>
										<td>{{ $v->name }}</td>
										<td>{{ $v->bb_source }}</td>
										<td>{{ $v->repo_type_name }} ({{ $v->repo_type_id }})</td>
										<td>{{ $v->created_at }}</td>
										<td>{{ $v->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addPlugin" tabindex="-1" role="dialog" aria-labelledby="addPluginLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_plugin')
				</div>
			</div>
		</div>
	</div>

@endsection