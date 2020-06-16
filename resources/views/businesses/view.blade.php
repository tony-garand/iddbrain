@extends('layouts.app')

@section('title', ' - Businesses - ' . $business->Company)

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1><a href="/businesses">Businesses</a> &gt; View Business</h1>
					</div>
					<div class="pull-right action">
						<a href="/businesses/queue_scan/{{ $business->id }}" class="btn btn-primary btn-small">Queue Yext Scan &gt;</a>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						@include('shared.forms.edit_business')
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection