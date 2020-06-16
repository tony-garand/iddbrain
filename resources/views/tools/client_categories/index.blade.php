@extends('layouts.app')

@section('title', ' - Client Categories')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Client Categories</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addRepoType">Add New Client Category &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Title</th>
									<th>Created</th>
									<th>Modified</th>
									<th>Actions</th>
								</tr>
							</thead>

							<tbody>
								@foreach($categories as $k => $v)
									<tr>
										<td>{{ $v->id }}</td>
										<td><i class="fa fa-square" style="color: {{ $v->color }};"></i> &nbsp;<a href="/tools/client-categories/view/{{ $v->id }}">{{ $v->title }}</a></td>
										<td>{{ $v->created_at }}</td>
										<td>{{ $v->updated_at }}</td>
										<td><a class="delete-link" href="/tools/client-categories/delete/{{ $v->id }}" data-title="{{ $v->title }}"><i class="fa fa-close"></i></a></td>
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
					@include('shared.forms.add_client_category')
				</div>
			</div>
		</div>
	</div>

@endsection