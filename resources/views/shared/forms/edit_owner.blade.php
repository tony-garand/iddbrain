<br/>
<form id="frm" class="form-horizontal" method="post" action="/owners/update">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="owner_name" class="col-sm-3 control-label">Owner Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Bob Vila" value="{{ $owner->owner_name }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="owner_email" class="col-sm-3 control-label">Owner Email:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="owner_email" name="owner_email" placeholder="bob@vila.com" value="{{ $owner->owner_email }}" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>