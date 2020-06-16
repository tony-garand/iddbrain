<br/>
<form id="frm" class="form-horizontal client-credential-form" method="post" action="/client-credentials/save" enctype="multipart/form-data">
	{{ csrf_field() }}
	<input type="hidden" name="client_id" value="{{ $client->id }}">

	<div class="form-group">
		<label for="type" class="col-sm-3 control-label">Type:</label>
		<div class="col-sm-8">
			<label class="radio-inline"><input class="tab-radio" type="radio" name="cred_type" value="text" data-target="#text-form" checked> Text</label>
			<label class="radio-inline"><input class="tab-radio" type="radio" name="cred_type" value="image" data-target="#img-form"> Image</label>
		</div>
	</div>

	<div class="tab-content">
		<div id="text-form" class="tab-pane active">
			<div class="form-group">
				<label for="name" class="col-sm-3 control-label">Label:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="label" name="label" placeholder="" value="{{ old('label') }}">
				</div>
			</div>
			<div class="form-group">
				<label for="color" class="col-sm-3 control-label">Value:</label>
				<div class="col-sm-8">
					<textarea class="form-control" id="value" name="value" rows="5" placeholder="">{{ old('value') }}</textarea>
				</div>
			</div>
		</div>

		<div id="img-form" class="tab-pane">
			<div class="form-group">
				<label for="screenshots" class="col-sm-3 control-label">Screenshots:</label>
				<div class="col-sm-8">
					<input class="form-control" type="file" id="screenshots" name="screenshots[]" multiple>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-8">
			<button id="add_btn" type="submit" class="btn btn-primary">Add New Credential</button>
		</div>
	</div>
</form>

<section class="credential-list">
	@if(count($creds))
	<div class="msg-box alert"></div>
	<div class="table-responsive">
		<br><br>
		<h4>Credentials</h4>
		<table class="table">
			<tbody id="credential-list">
			@foreach($creds as $cred)
			<tr data-id="{{ $cred->id }}">
				<td><i class="fa fa-arrows"></i> &nbsp;</td>
				<td><label><input class="form-control" type="text" value="{{ $cred->label }}" disabled></label></td>
				<td>
					<label>
						<textarea class="form-control" disabled>{{ $cred->value }}</textarea>
					</label>
				</td>
				<td>
					<button type="button" class="save-btn" data-client="{{ $client->id }}"><i class="fa fa-save"></i></button>
					<button type="button" class="edit-btn"><i class="fa fa-edit"></i></button>
					<button type="button" class="delete-btn" data-title="{{ $cred->label }}" data-client="{{ $client->id }}" data-type="text" data-id="{{ $cred->id }}"><i class="fa fa-close"></i></button>
				</td>
			</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	@endif
	@if(count($imgs))
	<h4>Screenshots</h4>
	<hr>
	<ul class="screenshots">
		@foreach($imgs as $img)
		<li>
			<form method="post" action="/client-credentials/delete/{{ $img->id }}">
				{{ csrf_field() }}
				<input type="hidden" name="cred_type" value="image">
				<input type="hidden" name="client_id" value="{{ $client->id }}">
				<button type="submit"><i class="fa fa-close"></i></button>
			</form>
			<a href="{{ $img->image_url }}" data-lightbox="creds">
				<img src="{{ $img->image_url }}" width="100">
			</a>
		</li>
		@endforeach
	</ul>
	@endif
</section>