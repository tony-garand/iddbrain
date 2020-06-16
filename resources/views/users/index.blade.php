@extends('layouts.app')

@section('title', ' - Users')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Users</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addUser">Add New User &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Email</th>
									<th>Role</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($users as $user)
									<tr>
										<td>{{ $user->id }}</td>
										<td><a href="/users/view/{{ $user->id }}">{{ $user->name }}</a></td>
										<td>{{ $user->email }}</td>
										<td>{{ $user->role_display_name }}</td>
										<td>{{ $user->created_at }}</td>
										<td>{{ $user->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="addUserLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_user')
				</div>
			</div>
		</div>
	</div>
@endsection