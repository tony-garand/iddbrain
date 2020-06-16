<br/>
<form id="frm" class="form-horizontal" method="post" action="/installs/update/{{ $install->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="bb_source" class="col-sm-3 control-label">Template:</label>
		<div class="col-sm-8">
			<label for="bb_source" class="control-label"> {{ $install->bb_source }} </label>
		</div>
	</div>

	<div class="form-group">
		<label for="bb_source" class="col-sm-3 control-label">Install Name:</label>
		<div class="col-sm-8">
			<label for="bb_source" class="control-label"> {{ $install->repo_name }} </label>
		</div>
	</div>

	<div id="plugins_block" class="form-group" style="display:none;">
		<label for="repo_name" class="col-sm-3 control-label">Plugins:</label>
		<div class="col-sm-8">
			<div class="plugins_list row">
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Site Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="site_name" name="site_name" placeholder="My Site Name" value="{{ $install->site_name }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="domain_url" class="col-sm-3 control-label">Domain:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="domain_url" name="domain_url" placeholder="www.bobvila.com" value="{{ $install->domain_url }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="client_id" class="col-sm-3 control-label">Client:</label>
		<div class="col-sm-8">
			<select class="form-control" id="client_id" name="client_id">
				<option value="0" selected>-- Select Client --</option>
				@foreach($clients as $client)
					<option value="{{ $client->id }}">{{ $client->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="owner_id" class="col-sm-3 control-label">Owner:</label>
		<div class="col-sm-8">
			<select class="form-control" id="owner_id" name="owner_id">
				<option value="0">Add New Owner</option>
				@foreach($owners as $owner)
					<option {{ (($owner->id == $install->owner_id) ? " selected " : "") }} value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="new_owner" style="display:none;">
		<div class="form-group">
			<label for="owner_name" class="col-sm-3 control-label">Owner Name:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Bob Vila" />
			</div>
		</div>
		<div class="form-group">
			<label for="owner_email" class="col-sm-3 control-label">Owner Email:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" id="owner_email" name="owner_email" placeholder="bob@vila.com" />
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="stackpath_id" class="col-sm-3 control-label">Stackpath ID:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="stackpath_id" name="stackpath_id" placeholder="123456" value="{{ $install->stackpath_id }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="node_ip" class="col-sm-3 control-label">Server IP Address:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="node_ip" name="node_ip" placeholder="127.0.0.1" value="{{ $install->node_ip }}" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
			<button data-href="/installs/delete/{{ $install->id }}" id="delete_btn" type="button" class="btn btn-primary btn-delete">Delete Install</button>
		</div>
	</div>
</form>