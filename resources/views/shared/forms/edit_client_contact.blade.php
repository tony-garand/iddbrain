<br/>
<form id="frm" class="form-horizontal client-credential-form" method="post" action="/client-contacts/update/{{ $contact->id }}">
	{{ csrf_field() }}
	<input type="hidden" name="client_id" value="{{ $client->id }}">

	<div class="form-group text-left">
		<label for="contact-type" class="col-sm-3 control-label">Contact Type:</label>
		<div class="col-sm-8">
			<input id="contact-type" data-options="{{ json_encode($types) }}" type="text" name="contact_type" value="{{ $contact->contact_type }}">
		</div>
	</div>
	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Name:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="name" name="name" value="{{ old('name') ?? $contact->name }}">
		</div>
	</div>
	<div class="form-group">
		<label for="occupation" class="col-sm-3 control-label">Occupation:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="occupation" name="occupation" value="{{ old('occupation') ?? $contact->occupation }}">
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">Email:</label>
		<div class="col-sm-8">
			<input class="form-control" type="email" id="email" name="email" value="{{ old('email') ?? $contact->email }}">
		</div>
	</div>
	<div class="form-group">
		<label for="mobile_phone" class="col-sm-3 control-label">Mobile Phone:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone') ?? $contact->mobile_phone }}">
		</div>
	</div>
	<div class="form-group">
		<label for="work_phone" class="col-sm-3 control-label">Work Phone:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="work_phone" name="work_phone" value="{{ old('work_phone') ?? $contact->work_phone }}">
		</div>
	</div>
	<div class="form-group">
		<label for="fax" class="col-sm-3 control-label">Fax:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="fax" name="fax" value="{{ old('fax') ?? $contact->fax }}">
		</div>
	</div>

	<div class="form-group">
		<label for="comments" class="col-sm-3 control-label">Comments:</label>
		<div class="col-sm-8">
			<textarea class="form-control" id="comments" name="comments">{{ old('comments') ?? $contact->comments }}</textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-8 text-left">
			<button id="add_btn" type="submit" class="btn btn-primary">Update Contact</button> &nbsp;
			<a href="/client-contacts/delete/{{ $contact->id }}?client_id={{ $client->id }}">Delete</a>
		</div>
	</div>
</form>