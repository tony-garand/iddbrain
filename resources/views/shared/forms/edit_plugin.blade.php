<br/>
<form id="frm" class="form-horizontal" method="post" action="/tools/plugins/update/{{ $plugin->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Plugin Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="" value="{{ $plugin->name }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="bb_source" class="col-sm-3 control-label">Source:</label>
		<div class="col-sm-8">
			<select class="form-control" id="bb_source" name="bb_source">
				<option value=""></option>
				@foreach($usable_repos as $usable_repo)
					<option {{ (($usable_repo == $plugin->bb_source) ? " selected " : "") }} value="{{ $usable_repo }}">{{ $usable_repo }}</option>
				@endforeach
			</select>
			<br/><label class="note"><b>Note:</b> This is a unique field, only one repo per source.</label>
		</div>
	</div>

	<div class="form-group">
		<label for="repo_type_id" class="col-sm-3 control-label">Repo Type:</label>
		<div class="col-sm-8">
			<select class="form-control" id="repo_type_id" name="repo_type_id">
				<option value=""></option>
				@foreach($repo_types as $repo_type)
					<option {{ (($repo_type->id == $plugin->repo_type_id) ? " selected " : "") }} value="{{ $repo_type->id }}">{{ $repo_type->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="description" class="col-sm-3 control-label">Plugin Description:</label>
		<div class="col-sm-8">
			<textarea rows="5" class="form-control" id="description" name="description">{{ $plugin->description }}</textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>