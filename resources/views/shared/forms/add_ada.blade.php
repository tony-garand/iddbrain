<br/>
<form id="frm" class="form-horizontal" method="post" action="/adas/save">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="key" class="col-sm-3 control-label">Key:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="key" name="key" placeholder="" />
			<br/><label class="note"><b>Note:</b> This is the key which will be used for the ada report.</label>
		</div>
	</div>

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Site Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="url" class="col-sm-3 control-label">URL:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="url" name="url" placeholder="http://" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>