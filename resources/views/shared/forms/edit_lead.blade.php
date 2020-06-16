<br/>
<form id="frm" class="form-horizontal" method="post" action="/leads/update/{{ $lead->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="analysis_key" class="col-sm-3 control-label">Analysis Key:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="analysis_key" name="analysis_key" value="{{ $lead->analysis_key }}" />
			<br/><label class="note"><b>Note:</b> This is the key which will be used for the analysis URL (ie. analysis.iddigital.us/[key])</label>
		</div>
	</div>

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Business Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" value="{{ $lead->name }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="address" class="col-sm-3 control-label">Business Address:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="address" name="address" value="{{ $lead->address }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="city" class="col-sm-3 control-label">Business City:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="city" name="city" value="{{ $lead->city }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="state" class="col-sm-3 control-label">Business State:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="state" name="state" value="{{ $lead->state }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="zip" class="col-sm-3 control-label">Business Zip:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="zip" name="zip" value="{{ $lead->zip }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="phone" class="col-sm-3 control-label">Business Phone:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="phone" name="phone" value="{{ $lead->phone }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="url" class="col-sm-3 control-label">URL:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="url" name="url" value="{{ $lead->url }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="contact_name" class="col-sm-3 control-label">Contact Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ $lead->contact_name }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="contact_phone" class="col-sm-3 control-label">Contact Phone:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ $lead->contact_phone }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="contact_email" class="col-sm-3 control-label">Contact Email:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="contact_email" name="contact_email" value="{{ $lead->contact_email }}" />
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 frm_subheader">
			<h5>Listing Scores</h5>
		</div>
	</div>

	<div class="form-group">
		<label for="listing_overall_percentage" class="col-sm-3 control-label">Overall:</label>
		<div class="col-sm-8">
			<input type="text" class="numeric form-control" id="listing_overall_percentage" name="listing_overall_percentage" value="{{ $lead->listing_overall_percentage }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="listing_business_name_percentage" class="col-sm-3 control-label">Business Name:</label>
		<div class="col-sm-8">
			<input type="text" class="numeric form-control" id="listing_business_name_percentage" name="listing_business_name_percentage" value="{{ $lead->listing_business_name_percentage }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="listing_address_percentage" class="col-sm-3 control-label">Address:</label>
		<div class="col-sm-8">
			<input type="text" class="numeric form-control" id="listing_address_percentage" name="listing_address_percentage" value="{{ $lead->listing_address_percentage }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="listing_phone_number_percentage" class="col-sm-3 control-label">Phone Number:</label>
		<div class="col-sm-8">
			<input type="text" class="numeric form-control" id="listing_phone_number_percentage" name="listing_phone_number_percentage" value="{{ $lead->listing_phone_number_percentage }}" />
		</div>
	</div>

	@if ($lead->yext_scan_url)
		<div class="form-group">
			<label for="yext_scan_url" class="col-sm-3 control-label">Yext Scan URL:</label>
			<div class="col-sm-8 control_content">
				<a target="_new" href="{{ $lead->yext_scan_url }}">{{ $lead->yext_scan_url }}</a>
			</div>
		</div>
	@endif

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="checkbox">
				<label>
					<input name="run_rescan" type="checkbox"> Re-pull scan?
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
			<button data-href="/leads/delete/{{ $lead->id }}" id="delete_btn" type="button" class="btn btn-primary btn-delete">Delete Lead</button>
		</div>
	</div>
</form>