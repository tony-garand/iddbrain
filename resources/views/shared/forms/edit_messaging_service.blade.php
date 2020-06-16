<br/>
<form id="frm" class="form-horizontal" method="post" action="/tools/messaging_services/update/{{ $messaging_service->id }}">
{{ csrf_field() }}

	<div class="form-group">
		<label for="client_id" class="col-sm-3 control-label">Client:</label>
		<div class="col-sm-8">
			<select class="form-control" id="client_id" name="client_id">
				<option value=""></option>
				@foreach($clients as $client)
					<option {{ (($client->id == $messaging_service->client_id) ? " selected " : "") }} value="{{ $client->id }}">{{ $client->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>