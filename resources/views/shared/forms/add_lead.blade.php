<br/>
<form id="frm" class="form-horizontal" method="post" action="/leads/save">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="analysis_key" class="col-sm-3 control-label">Analysis Key:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="analysis_key" name="analysis_key" placeholder="" />
			<br/><label class="note"><b>Note:</b> This is the key which will be used for the analysis URL (ie. analysis.iddigital.us/[key])</label>
		</div>
	</div>

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Business Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="address" class="col-sm-3 control-label">Business Address:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="address" name="address" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="city" class="col-sm-3 control-label">Business City:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="city" name="city" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="state" class="col-sm-3 control-label">Business State:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="state" name="state" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="zip" class="col-sm-3 control-label">Business Zip:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="zip" name="zip" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="phone" class="col-sm-3 control-label">Business Phone:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control numonly" id="phone" name="phone" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="url" class="col-sm-3 control-label">URL:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="url" name="url" placeholder="http://" />
		</div>
	</div>

	<div class="form-group">
		<label for="contact_name" class="col-sm-3 control-label">Contact Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="contact_phone" class="col-sm-3 control-label">Contact Phone:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control numonly" id="contact_phone" name="contact_phone" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="contact_email" class="col-sm-3 control-label">Contact Email:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>