<br/>
<form id="frm" class="form-horizontal" method="post" action="/installs/save">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="bb_source" class="col-sm-3 control-label">Template:</label>
		<div class="col-sm-8">
			<select class="form-control" id="bb_source" name="bb_source">
				<option value=""></option>
				<option value="no-template">No Template/Exists Already</option>
				@foreach($usable_repos as $usable_repo)
					<option value="{{ $usable_repo }}">{{ $usable_repo }}</option>
				@endforeach
			</select>
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
		<label for="repo_name" class="col-sm-3 control-label">Install Name:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="repo_name" name="repo_name" placeholder="MyRepoName" />
		</div>
	</div>

	<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Site Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="site_name" name="site_name" placeholder="My Site Name" />
		</div>
	</div>

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

	<div class="form-group">
		<label for="client_id" class="col-sm-3 control-label">Client:</label>
		<div class="col-sm-8">
			<select class="form-control" id="client_id" name="client_id">
				<option selected>-- Select Client --</option>
				@foreach($clients as $client)
					<option value="{{ $client->id }}">{{ $client->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="domain_url" class="col-sm-3 control-label">Domain:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="domain_url" name="domain_url" placeholder="www.bobvila.com" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="checkbox">
				<label>
					<input name="node_staging" checked type="checkbox"> Spin up Staging
				</label>
			</div>
		</div>
	</div>

	{{--<div class="form-group">--}}
		{{--<div class="col-sm-offset-3 col-sm-9">--}}
			{{--<div class="checkbox">--}}
				{{--<label>--}}
					{{--<input name="node_production" type="checkbox"> Spin up Production--}}
				{{--</label>--}}
			{{--</div>--}}
		{{--</div>--}}
	{{--</div>--}}

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>