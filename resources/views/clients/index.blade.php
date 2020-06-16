@extends('layouts.app')

@section('title', ' - Clients')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Clients</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addClient">Add New Client &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<div class="loadin"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>
						<table style="display:none;" class="datatable table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th class="sort_by" data-sort_order="asc">Client Name</th>
									<th>Categories</th>
									<th>Client URL</th>
									<th>MM?</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($clients as $client)
									<tr>
										<td>{{ $client->id }}</td>
										<td><a href="/clients/view/{{ $client->id }}">{{ $client->name }}</a></td>
										<td>
											@foreach($client->cats as $cat)
												<span class="cat-tags" style="background-color: {{ $cat->color }}">{{ $cat->title }}</span>
											@endforeach
										</td>
										<td>{{ $client->url }}</td>
										<td>{{ format_bool_yn($client->is_mm) }}</td>
										<td>{{ $client->created_at }}</td>
										<td>{{ $client->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addClient" tabindex="-1" role="dialog" aria-labelledby="addClientLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_client')
				</div>
			</div>
		</div>
	</div>
@endsection