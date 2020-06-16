@extends('layouts.app')

@section('title', ' - Repos - ' . $repo->bb_source)

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>View Repo</h1>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						@include('shared.forms.edit_repo')
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection