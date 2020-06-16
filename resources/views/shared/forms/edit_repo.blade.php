<br/>
<form id="frm" class="form-horizontal" method="post" action="/tools/repos/update/{{ $repo->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="theme_name" class="col-sm-3 control-label">Theme Name:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="theme_name" name="theme_name" placeholder="My Theme Name" value="{{ $repo->theme_name }}" />
			<br/><label class="note"><b>Note:</b> This will be used for naming the "theme" folder when deploying to the server.</label>
		</div>
	</div>

	<div class="form-group">
		<label for="bb_source" class="col-sm-3 control-label">Source:</label>
		<div class="col-sm-8">
			<select class="form-control" id="bb_source" name="bb_source">
				<option value=""></option>
				@foreach($usable_repos as $usable_repo)
					<option {{ (($usable_repo == $repo->bb_source) ? " selected " : "") }} value="{{ $usable_repo }}">{{ $usable_repo }} </option>
				@endforeach
			</select>
			<br/><label class="note"><b>Note:</b> This is a unique field, only one repo per source.</label>
		</div>
	</div>

	<div class="form-group">
		<label for="root_source" class="col-sm-3 control-label">Parent Source:</label>
		<div class="col-sm-8">
			<select class="form-control" id="root_source" name="root_source">
				<option value=""></option>
				@foreach($root_sources as $root_source)
					<option {{ (($root_source == $repo->root_source) ? " selected " : "") }} value="{{ $root_source }}">{{ $root_source }} </option>
				@endforeach
			</select>
			<br/><label class="note"><b>Note:</b> This is the "parent" (root) source for the child theme, if applicable.</label>
		</div>
	</div>

	<div class="form-group">
		<label for="repo_type_id" class="col-sm-3 control-label">Repo Type:</label>
		<div class="col-sm-8">
			<select class="form-control" id="repo_type_id" name="repo_type_id">
				<option value=""></option>
				@foreach($repo_types as $repo_type)
					<option {{ (($repo_type->id == $repo->repo_type_id) ? " selected " : "") }} value="{{ $repo_type->id }}">{{ $repo_type->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="wrap_ups" class="col-sm-3 control-label">Repo Wrap Ups:</label>
		<div class="col-sm-8">
			<textarea rows="5" class="form-control" id="wrap_ups" name="wrap_ups">{{ $repo->wrap_ups }}</textarea>
			<br/><label class="note"><b>Note:</b> Wrap ups are commands that are run once this repo is deployed.</label>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>