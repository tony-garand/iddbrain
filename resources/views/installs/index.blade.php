@extends('layouts.app')

@section('title', ' - Installs')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Installs</h1>
					</div>
					<div class="pull-right action">
						<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addInstall">Add New Install &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<div class="loadin"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>
						<table style="display:none;" class="datatable table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th class="sort_by" data-sort_order="asc">Site Name</th>
									<th>Template</th>
									<th>Client</th>
									<th>Owner</th>
									<th>Domain</th>
									<th>Modified</th>
									<th></th>
							</thead>
							<tbody>
								@foreach($installs as $k => $v)
									<tr>
										<td>{{ $v->id }}</td>
										<td><a href="/installs/view/{{ $v->id }}">{{ $v->site_name }}</a></td>
										<td>{{ $v->bb_source }}</td>
										<td>
											@if(!empty($v->client_id))
											<a href="/clients/view/{{ $v->client_id }}">{{ $v->client_name }}</a>
											@else
												N/A
											@endif
										</td>
										<td><a href="/owners/view/{{ $v->owner_id }}">{{ $v->owner_name }}</a></td>
										<td>{{ $v->domain_url }}</td>
										<td>{{ $v->updated_at }}</td>
										<td>
											@if ($v->node_staging == "on")

												@if ($v->repo_type_id == 1)
													<a href="http://{{ $v->repo_name }}.staging.iddigital.me/" target="_new"><i class="fa fa-globe" aria-hidden="true"></i> staging</a>
												@endif

												@if ($v->repo_type_id == 2)
													<a href="http://{{ $v->repo_name }}.wpstaging.iddigital.me/" target="_new"><i class="fa fa-globe" aria-hidden="true"></i> wp staging</a>
												@endif

												@if ($v->repo_type_id == 3)
													<a href="http://{{ $v->repo_name }}.craft3.staging.iddigital.me/" target="_new"><i class="fa fa-globe" aria-hidden="true"></i> c3 staging</a>
												@endif

											@endif
											@if ($v->node_production == "on")
												<a href="http://{{ $v->repo_name }}.iddigital.me/" target="_new"><i class="fa fa-globe" aria-hidden="true"></i> production</a>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addInstall" tabindex="-1" role="dialog" aria-labelledby="addInstallLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					@include('shared.forms.add_install')
				</div>
			</div>
		</div>
	</div>

@endsection