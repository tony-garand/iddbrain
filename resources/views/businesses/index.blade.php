@extends('layouts.app')

@section('title', ' - Businesses')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>Businesses</h1>
					</div>
					{{--<div class="pull-right action">--}}
						{{--<a href="javascript:;" class="btn btn-primary" data-toggle="modal" data-target="#addServer">Add New Business &gt;</a>--}}
					{{--</div>--}}
				</div>

				<div class="panel panel-default">
					<div class="panel-body">

						<div class="loadin"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>
						<table style="display:none;" class="datatable table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th class="sort_by" data-sort_order="asc">Name</th>
									<th>Address</th>
									<th>City</th>
									<th>State</th>
									<th>URL</th>
									<th>Status</th>
							</thead>
							<tbody>
								@foreach($businesses as $business)
									<tr>
										<td><a href="/businesses/view/{{ $business->id }}">{{ $business->id }}</a></td>
										<td><a href="/businesses/view/{{ $business->id }}">{{ $business->Company }}</a></td>
										<td>{{ $business->Address }}</td>
										<td>{{ $business->City }}</td>
										<td>{{ $business->Mailing_State }}</td>
										<td>{{ $business->URL }}</td>
										<td>{{ format_business_status($business->status) }}</td>
									</tr>
								@endforeach
							</tbody>

						</table>

					</div>
				</div>
			</div>
		</div>
	</div>

@endsection