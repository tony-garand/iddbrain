@extends('layouts.app')

@section('title', ' - Clients - ' . $client->name)

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1><a href="/clients">Clients</a> - {{ $client->name }}</h1>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-11">
								<ul class="nav nav-pills tabs-right" role="tablist">
									<li role="presentation"><a href="#notes" aria-controls="notes" role="tab" data-toggle="pill">Notes</a></li>
									<li role="presentation"><a href="#contacts" aria-controls="contacts" role="tab" data-toggle="pill">Contacts</a></li>
									<li role="presentation"><a href="#credentials" aria-controls="credentials" role="tab" data-toggle="pill">Credentials</a></li>
									<li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="pill">Details</a></li>
								</ul>
								<br>
							</div>
						</div>

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="details">
								@include('shared.forms.edit_client')
							</div>
							<div role="tabpanel" class="tab-pane fade" id="credentials">
								@include('shared.forms.add_client_credential')
							</div>
							<div role="tabpanel" class="tab-pane fade" id="contacts">
								@include('shared.forms.add_client_contact')
							</div>
							<div role="tabpanel" class="tab-pane fade" id="notes">
								@include('shared.forms.add_client_note')
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection