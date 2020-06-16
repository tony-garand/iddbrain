@extends('layouts.app')

@section('title', '- ADA')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>ADA Compliance</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addADA">Add ADA Check &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<div class="loadin"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>
						<table style="display:none;" class="datatable table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th class="sort_by" data-sort_order="asc">Name</th>
									<th>Analysis Key</th>
									<th>URL</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($adas as $ada)
									<tr>
										<td>{{ $ada->id }}</td>
										<td><a href="/adas/view/{{ $ada->id }}">{{ $ada->name }}</a></td>
										<td>
											<a title="IDD Analysis" href="/reports/{{ $ada->key }}.report.html" target="_new">
												@if ($ada->status == 0)<i class="fa fa-circle-o-notch fa-spin fa-spin-loading globe-idd" aria-hidden="true"></i>@else<i class="fa fa-globe globe-idd" aria-hidden="true"></i>@endif
												{{ $ada->key }}
											</a>
										</td>
										<td>{{ $ada->domain }}</td>
										<td>{{ $ada->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addADA" tabindex="-1" role="dialog" aria-labelledby="addADALabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_ada')
				</div>
			</div>
		</div>
	</div>
@endsection