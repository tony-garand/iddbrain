@extends('layouts.app')

@section('title', ' - Repos')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Repos</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addRepo">Add New Repo &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>BitBucket Source</th>
									<th>Repo Type</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($repos as $k => $v)
									<tr>
										<td><a href="/tools/repos/view/{{ $v->id }}">{{ $v->id }}</a></td>
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

	<div class="modal fade" id="addRepo" tabindex="-1" role="dialog" aria-labelledby="addRepoLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_repo')
				</div>
			</div>
		</div>
	</div>

@endsection