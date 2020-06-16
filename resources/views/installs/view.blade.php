@extends('layouts.app')

@section('title', ' - Installs - ' . $install->site_name)

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1>View Install</h1>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						@include('shared.forms.edit_install')
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {

			$('#owner_id').on('change', function() {
				var owner_id = $(this).val();
				if (owner_id == 0) {
					$('.new_owner').fadeIn();
				} else {
					$('.new_owner').hide();
				}
			});

		});
	</script>

@endsection