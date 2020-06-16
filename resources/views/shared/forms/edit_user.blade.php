<br/>
<form id="frm" class="form-horizontal" method="post" action="/users/update/{{ $user->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Full Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">Email:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="role_id" class="col-sm-3 control-label">Role:</label>
		<div class="col-sm-8">
			<select class="form-control" id="role_id" name="role_id">
				@foreach ($roles as $role)
					<option {{ (($role->id == $user->user_role) ? " selected " : "") }} value="{{ $role->id }}">{{ $role->display_name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-3 control-label">Password:</label>
		<div class="col-sm-8">
			<input type="password" class="form-control" id="password" name="password" placeholder="" />
			<br/><label class="note"><b>Note:</b> Only enter password if you want to update the users password.</label>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="update_btn" type="submit" class="btn btn-primary">Update</button>
			@if ($user->id > "1")
			<button data-href="/users/delete/{{ $user->id }}" id="delete_btn" type="button" class="btn btn-primary btn-delete">Delete User</button>
			@endif
		</div>
	</div>
</form>