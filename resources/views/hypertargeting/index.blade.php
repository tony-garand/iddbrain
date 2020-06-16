@extends('layouts.app')

@section('title', ' - HyperTargeting')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>HyperTargeting</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addHT">Add New &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Path</th>
									<th>Status</th>
									<th>Created</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($hypertargeting as $ht)
									<tr>
										<td>{{ $ht->id }}</td>
										<td><a href="/hypertargeting/view/{{ $ht->id }}">{{ $ht->name }}</a></td>
										<td><a href="{{ hypertargeting_domain($ht->default_domain, env('HT_PUBLIC_URL')) }}/{{ $ht->repo_name }}" target="_new">{{ $ht->repo_name }}</a></td>
										<td>{{ ht_status($ht->status) }}</td>
										<td>{{ $ht->created_at }}</td>
										<td>{{ $ht->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addHT" tabindex="-1" role="dialog" aria-labelledby="addHTLabel">
		<div class="modal-dialog modal-dialog-wide" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_hypertargeting')
				</div>
			</div>
		</div>
	</div>
@endsection
