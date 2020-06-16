@extends('layouts.app')

@section('title', ' - Client Categories - ' . $category->title)

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1><a href="/tools/client-categories">Client Categories</a> - {{ $category->title }}</h1>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						@include('shared.forms.edit_client_category')
					</div>
				</div>

			</div>
		</div>
	</div>
@endsection