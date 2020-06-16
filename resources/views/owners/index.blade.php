@extends('layouts.app')

@section('title', ' - Owners')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Owners</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addOwner">Add New Owner &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Owner Name</th>
									<th>Owner Email</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($owners as $owner)
									<tr>
										<td>{{ $owner->id }}</td>
										<td><a href="/owners/view/{{ $owner->id }}">{{ $owner->owner_name }}</a></td>
										<td>{{ $owner->owner_email }}</td>
										<td>{{ $owner->created_at }}</td>
										<td>{{ $owner->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addOwner" tabindex="-1" role="dialog" aria-labelledby="addOwnerLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_owner')
				</div>
			</div>
		</div>
	</div>
@endsection