@extends('layouts.app')

@section('title', ' - HyperTargeting - ' . $hypertargeting->name)

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1><a href="/hypertargeting">HyperTargeting</a> &gt; View HyperTargeting</h1>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						@include('shared.forms.edit_hypertargeting')
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
