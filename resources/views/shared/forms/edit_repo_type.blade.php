<br/>
<form id="frm" class="form-horizontal" method="post" action="/tools/repo_types/update/{{ $repo_type->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Type Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" value="{{ $repo_type->name }}" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>