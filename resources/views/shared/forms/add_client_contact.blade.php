<br/>
<form id="frm" class="form-horizontal client-credential-form" method="post" action="/client-contacts/save">
	{{ csrf_field() }}
	<input type="hidden" name="client_id" value="{{ $client->id }}">

	<div class="form-group">
		<label for="contact-type" class="col-sm-3 control-label">Contact Type:</label>
		<div class="col-sm-8">
			<input id="contact-type" data-options="{{ json_encode($types) }}" type="text" name="contact_type">
		</div>
	</div>
	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Name:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}">
		</div>
	</div>
	<div class="form-group">
		<label for="occupation" class="col-sm-3 control-label">Occupation:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="occupation" name="occupation" value="{{ old('occupation') }}">
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">Email:</label>
		<div class="col-sm-8">
			<input class="form-control" type="email" id="email" name="email" value="{{ old('email') }}">
		</div>
	</div>
	<div class="form-group">
		<label for="mobile_phone" class="col-sm-3 control-label">Mobile Phone:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="mobile_phone" name="mobile_phone" value="{{ old('mobile_phone') }}">
		</div>
	</div>
	<div class="form-group">
		<label for="work_phone" class="col-sm-3 control-label">Work Phone:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="work_phone" name="work_phone" value="{{ old('work_phone') }}">
		</div>
	</div>
	<div class="form-group">
		<label for="fax" class="col-sm-3 control-label">Fax:</label>
		<div class="col-sm-8">
			<input class="form-control" type="text" id="fax" name="fax" value="{{ old('fax') }}">
		</div>
	</div>

	<div class="form-group">
		<label for="comments" class="col-sm-3 control-label">Comments:</label>
		<div class="col-sm-8">
			<textarea class="form-control" id="comments" name="comments">{{ old('comments') }}</textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-8">
			<button id="add_btn" type="submit" class="btn btn-primary">Add New Contact</button>
		</div>
	</div>
</form>

<br><br>
@if(count($contacts))
<section class="contact-list">
	<div class="row">
		<div class="col-sm-12">
			<h4>Contacts</h4><hr>
		</div>
		@foreach($contacts as $contact)
			<div class="col-sm-12 col-md-4 text-center">
				<ul data-toggle="tooltip" data-placement="top" title="<strong>Comments</strong><br>{{ $contact->comments }}">
					<li>
						<h4>
							{{ $contact->name }}
							<sup><a data-toggle="modal" href="#client-contact-{{ $contact->id }}"><i class="fa fa-edit"></i></a></sup>
						</h4>
					<li><i>{{ $contact->occupation ?? 'N/A' }}</i><sup></sup></li>
					<li>
						@foreach(explode(',', $contact->contact_type) as $type)
							<span class="type">{{ $type }}</span>
						@endforeach
					</li>
					@if(!empty($contact->email))
						<li><i class="fa fa-envelope"></i> {{ $contact->email }}</li>
					@endif
					@if(!empty($contact->mobile_phone))
						<li><i class="fa fa-phone"></i> {{ $contact->mobile_phone }}</li>
					@endif
					@if(!empty($contact->work_phone))
						<li><i class="fa fa-building"></i> {{ $contact->work_phone }}</li>
					@endif
					@if(!empty($contact->fax))
						<li><i class="fa fa-fax"></i> {{ $contact->fax }}</li>
					@endif
				</ul>

				<div class="modal fade client-contact-modal" id="client-contact-{{ $contact->id }}" tabindex="-1" role="dialog" aria-labelledby="client-contact-{{ $contact->id }}">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-body">
								@include('shared.forms.edit_client_contact')
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</section>
@endif