<br/>
<form id="frm" class="form-horizontal" method="post" action="/owners/save">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="repo_name" class="col-sm-3 control-label">Owner Name:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="repo_name" name="repo_name" placeholder="MyRepoName" />
		</div>
	</div>

	<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Owner Email:</label>
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
		<label for="domain_url" class="col-sm-3 control-label">Domain:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="domain_url" name="domain_url" placeholder="www.bobvila.com" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>