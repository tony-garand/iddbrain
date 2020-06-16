@extends('layouts.app')

@section('title', ' - Repo Types')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Repo Types</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addRepoType">Add New Repo Type &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Type Name</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($repo_types as $k => $v)
									<tr>
										<td><a href="/tools/repo_types/view/{{ $v->id }}">{{ $v->id }}</a></td>
										<td>{{ $v->name }}</td>
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

	<div class="modal fade" id="addRepoType" tabindex="-1" role="dialog" aria-labelledby="addRepoTypeLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_repo_type')
				</div>
			</div>
		</div>
	</div>

@endsection