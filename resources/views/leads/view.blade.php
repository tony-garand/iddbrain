@extends('layouts.app')

@section('title', ' - Leads - ' . $lead->name)

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				@include('shared.messages')

				<div class="actions">
					<div class="pull-left info">
						<h1><a href="/leads">Leads</a> &gt; View Lead</h1>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						@include('shared.forms.edit_lead')
					</div>
				</div>

				@if ($business)
				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Associated Business</h5>

						<form id="frm" class="form-horizontal" method="post" action="/leads/update/{{ $lead->id }}">

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

						@if ($business->yext_scan_url)
							<div class="form-group">
								<label for="Company" class="col-sm-3 control-label">Yext Scan URL:</label>
								<div class="col-sm-8 control_content">
									<a target="_new" href="{{ $business->yext_scan_url }}">{{ $business->yext_scan_url }}</a>
								</div>
							</div>
						@endif

						<div class="form-group">
							<label for="Company" class="col-sm-3 control-label">Business Name:</label>
							<div class="col-sm-8 control_content">
								{{ $business->Company }}
							</div>
						</div>

						<div class="form-group">
							<label for="Address" class="col-sm-3 control-label">Address:</label>
							<div class="col-sm-8 control_content">
								{{ $business->Address }}
							</div>
						</div>

						<div class="form-group">
							<label for="City" class="col-sm-3 control-label">City:</label>
							<div class="col-sm-8 control_content">
								{{ $business->City }}
							</div>
						</div>

						<div class="form-group">
							<label for="Mailing_State" class="col-sm-3 control-label">State:</label>
							<div class="col-sm-8 control_content">
								{{ $business->Mailing_State }}
							</div>
						</div>

						<div class="form-group">
							<label for="Zip" class="col-sm-3 control-label">Zip:</label>
							<div class="col-sm-8 control_content">
								{{ $business->Zip }}
							</div>
						</div>

						<div class="form-group">
							<label for="CountyName" class="col-sm-3 control-label">County:</label>
							<div class="col-sm-8 control_content">
								{{ $business->CountyName }}
							</div>
						</div>

						<div class="form-group">
							<label for="PRIMARY_SIC_DESC" class="col-sm-3 control-label">Categories:</label>
							<div class="col-sm-8 control_content">
								{{ $business->PRIMARY_SIC_DESC }}
							</div>
						</div>

						<div class="form-group">
							<label for="lat" class="col-sm-3 control-label">Latitude:</label>
							<div class="col-sm-8 control_content">
								{{ $business->lat }}
							</div>
						</div>

						<div class="form-group">
							<label for="lng" class="col-sm-3 control-label">Longitude:</label>
							<div class="col-sm-8 control_content">
								{{ $business->lng }}
							</div>
						</div>

						<div class="form-group">
							<label for="lng" class="col-sm-3 control-label">Map:</label>
							<div class="col-sm-8">
								<div id="map"></div>
							</div>
						</div>

						</form>

					</div>
				</div>
				@endif


				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Scan Summary</h5>

						@if ($lead->overall_status == 0)
							<div class="loading">
								<i class="fa fa-circle-o-notch fa-spin fa-spin-loading"></i> Loading updated content...
							</div>
						@else
							@if (($source_google_desktop) && ($source_google_mobile))
								<div class="pan">
									Desktop Speed Score: <b>{{ @json_decode(json_encode(unserialize(@$source_google_desktop->source_data), true))->ruleGroups->SPEED->score }} / 100</b>
								</div>
								<div class="pan">
									Mobile Speed Score: <b>{{ @json_decode(json_encode(unserialize(@$source_google_mobile->source_data), true))->ruleGroups->SPEED->score }} / 100</b>
								</div>
								<div class="pan">
									Mobile Usability Score: <b>{{ @json_decode(json_encode(unserialize(@$source_google_mobile->source_data), true))->ruleGroups->USABILITY->score }} / 100</b>
								</div>
							@else
								No data found
							@endif
						@endif

					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Image Captures</h5>

						@if ($lead->overall_status == 0)
							<div class="loading">
								<i class="fa fa-circle-o-notch fa-spin fa-spin-loading"></i> Loading updated content...
							</div>
						@else
							@if (($source_google_desktop) && ($source_google_mobile))
								<div class="imagecap" style="width:520px;">
									<img data-featherlight="{{ env('SS_HOST_URL') }}/screenshots/{{ $lead->id }}_1024x640.png" src="{{ env('SS_HOST_URL') }}/screenshots/{{ $lead->id }}_1024x640.png" width="512" style="border:2px solid #000;" />
								</div>
								<div class="imagecap" style="width:370px;">
									<img data-featherlight="{{ env('SS_HOST_URL') }}/screenshots/{{ $lead->id }}_360x640.png" src="{{ env('SS_HOST_URL') }}/screenshots/{{ $lead->id }}_360x640.png" width="268" style="border:2px solid #000;" />
								</div>
							@else
								No data found
							@endif
						@endif

					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Yext Listings</h5>

						@if ($lead->overall_status == 0)
							<div class="loading">
								<i class="fa fa-circle-o-notch fa-spin fa-spin-loading"></i> Loading updated content...
							</div>
						@else

							@if (count($listings) > 0)

								@foreach($listings as $listing)
									<div class="col-md-4 listing_block">
										<div class="inner_block">
											<h6>{{ $listing->listing_type }}</h6>
											@if ($listing->is_missing)
												<div class="is_missing"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Not found!</div>
											@else
												<div class="rec">@if ($listing->listing_name_status == 1)<i class="fa fa-check-circle" aria-hidden="true"></i>@else<i class="fa fa-times-circle" aria-hidden="true"></i>@endif {{ $listing->listing_name }}</div>
												<div class="rec">@if ($listing->listing_address_status == 1)<i class="fa fa-check-circle" aria-hidden="true"></i>@else<i class="fa fa-times-circle" aria-hidden="true"></i>@endif {{ $listing->listing_address }}</div>
												<div class="rec">@if ($listing->listing_phone_status == 1)<i class="fa fa-check-circle" aria-hidden="true"></i>@else<i class="fa fa-times-circle" aria-hidden="true"></i>@endif {{ $listing->listing_phone }}</div>
											@endif
										</div>
									</div>
								@endforeach

							@else
								No listings found
							@endif

						@endif

					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Desktop Information</h5>
						@if ($lead->overall_status == 0)
							<div class="loading">
								<i class="fa fa-circle-o-notch fa-spin fa-spin-loading"></i> Loading updated content...
							</div>
						@else
							<div class="clipd">
								@if ($source_google_desktop)
									<textarea class="yext_outpout">{{ print_r(unserialize(@$source_google_desktop->source_data)) }}</textarea>
								@else
									No data found
								@endif
							</div>
						@endif
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Mobile Information</h5>
						@if ($lead->overall_status == 0)
							<div class="loading">
								<i class="fa fa-circle-o-notch fa-spin fa-spin-loading"></i> Loading updated content...
							</div>
						@else
							<div class="clipd">
								@if ($source_google_mobile)
									<textarea class="yext_outpout">{{ print_r(unserialize(@$source_google_mobile->source_data)) }}</textarea>
								@else
									No data found
								@endif
							</div>
						@endif
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<h5>Yext Scan Information</h5>
						@if ($lead->overall_status == 0)
							<div class="loading">
								<i class="fa fa-circle-o-notch fa-spin fa-spin-loading"></i> Loading updated content...
							</div>
						@else
							<div class="clipd">
								@if ($source_yext)
									<textarea class="yext_outpout">{{ print_r(unserialize(@$source_yext->source_data)) }}</textarea>
								@else
									No data found
								@endif
							</div>
						@endif
					</div>
				</div>


			</div>
		</div>
	</div>
@endsection