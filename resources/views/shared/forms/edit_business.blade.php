<br/>
<form id="frm" class="form-horizontal" method="post" action="/businesses/update/{{ $business->id }}">
	{{ csrf_field() }}

	<script>
	function initMap() {
		var myLatLng = {lat: {{ $business->lat }}, lng: {{ $business->lng }}};
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 16,
			center: myLatLng
		});

		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			title: 'Hello World!'
		});
	}
	</script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_API_KEY') }}&callback=initMap"></script>

	<div class="form-group">
		<label for="Company" class="col-sm-3 control-label">Business Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="Company" name="Company" value="{{ $business->Company }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="FULLNAME1" class="col-sm-3 control-label">Contact Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="FULLNAME1" name="FULLNAME1" value="{{ $business->FULLNAME1 }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="Phone" class="col-sm-3 control-label">Phone:</label>
		<div class="col-sm-8">
			<input type="text" class="numonly form-control" id="Phone" name="Phone" value="{{ $business->Phone }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="URL" class="col-sm-3 control-label">URL:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="URL" name="URL" value="{{ $business->URL }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="Address" class="col-sm-3 control-label">Address:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="Address" name="Address" value="{{ $business->Address }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="City" class="col-sm-3 control-label">City:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="City" name="City" value="{{ $business->City }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="Mailing_State" class="col-sm-3 control-label">State:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="Mailing_State" name="Mailing_State" value="{{ $business->Mailing_State }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="Zip" class="col-sm-3 control-label">Zip:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="Zip" name="Zip" value="{{ $business->Zip }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="CountyName" class="col-sm-3 control-label">County:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="CountyName" name="CountyName" value="{{ $business->CountyName }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="PRIMARY_SIC_DESC" class="col-sm-3 control-label">Categories:</label>
		<div class="col-sm-8">
			<textarea rows="3" class="form-control" id="PRIMARY_SIC_DESC" name="PRIMARY_SIC_DESC">{{ $business->PRIMARY_SIC_DESC }}</textarea>
		</div>
	</div>

	<div class="form-group">
		<label for="status" class="col-sm-3 control-label">Status:</label>
		<div class="col-sm-8">
			<select class="form-control" id="status" name="status">
				<option {{ (($business->status == 0) ? " selected " : "") }} value="0">New</option>
				<option {{ (($business->status == 1) ? " selected " : "") }} value="1">Ready</option>
				<option {{ (($business->status == 10) ? " selected " : "") }} value="10">Claimed</option>
				<option {{ (($business->status == 20) ? " selected " : "") }} value="20">Contacted</option>
				<option {{ (($business->status == -1) ? " selected " : "") }} value="-1">Closed</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="lat" class="col-sm-3 control-label">Latitude:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="lat" name="lat" value="{{ $business->lat }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="lng" class="col-sm-3 control-label">Longitude:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="lng" name="lng" value="{{ $business->lng }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="lng" class="col-sm-3 control-label">Map:</label>
		<div class="col-sm-8">
			<div id="map"></div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>