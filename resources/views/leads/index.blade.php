@extends('layouts.app')

@section('title', '- Leads')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Leads</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addLead">Add New Lead &gt;</a>
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
									<th>Contact Name</th>
									<th>Contact Phone</th>
									<th>Modified</th>
								</tr>
							</thead>

							<tbody>
								@foreach($leads as $lead)
									<tr>
										<td>{{ $lead->id }}</td>
										<td><a href="/leads/view/{{ $lead->id }}">{{ $lead->name }}</a></td>
										<td>
											<a title="IDD Analysis" href="{{ env('ANALYSIS_HOST_URL') }}/{{ $lead->analysis_key }}" target="_new">@if ($lead->overall_status == 0)<i class="fa fa-circle-o-notch fa-spin fa-spin-loading globe-idd" aria-hidden="true"></i>@else<i class="fa fa-globe globe-idd" aria-hidden="true"></i>@endif</a>
											<a title="Soar Analysis" href="{{ env('SOAR_ANALYSIS_HOST_URL') }}/{{ $lead->analysis_key }}" target="_new">@if ($lead->overall_status == 0)<i class="fa fa-circle-o-notch fa-spin fa-spin-loading globe-soar" aria-hidden="true"></i>@else<i class="fa fa-globe globe-soar" aria-hidden="true"></i>@endif</a>
											{{ $lead->analysis_key }}
										</td>
										<td>{{ $lead->url }}</td>
										<td>{{ $lead->contact_name }}</td>
										<td>{{ $lead->contact_phone }}</td>
										<td>{{ $lead->updated_at }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addLead" tabindex="-1" role="dialog" aria-labelledby="addLeadLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_lead')
				</div>
			</div>
		</div>
	</div>
@endsection