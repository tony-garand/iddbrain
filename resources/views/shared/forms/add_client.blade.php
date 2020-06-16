<br/>
<form id="frm" class="form-horizontal" method="post" action="/clients/save">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Client Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="Client Name" />
		</div>
	</div>

	<div class="form-group">
		<label for="url" class="col-sm-3 control-label">URL:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="url" name="url" placeholder="www.bobvila.com" />
		</div>
	</div>

	<div class="form-group">
		<label for="description" class="col-sm-3 control-label">Description:</label>
		<div class="col-sm-8">
			<textarea rows="5" class="form-control" id="description" name="description"></textarea>
		</div>
	</div>

	<div class="form-group">
		<label for="billable_rate" class="col-sm-3 control-label">Billable Rate:</label>
		<div class="col-sm-8">
			<div class="input-group">
				<div class="input-group-addon">$</div>
				<input type="text" class="form-control" id="billable_rate" name="billable_rate">
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="hosting" class="col-sm-3 control-label">Hosting:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="hosting" name="hosting" placeholder="ex. - Digital Ocean" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="checkbox">
				<label>
					<input name="is_mm" type="checkbox"> Message Management?
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>