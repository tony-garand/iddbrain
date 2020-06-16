<br/>
<form id="frm" class="form-horizontal" method="post" action="/users/save">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Full Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="email" class="col-sm-3 control-label">Email:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="email" name="email" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="password" class="col-sm-3 control-label">Password:</label>
		<div class="col-sm-8">
			<input type="password" class="form-control" id="password" name="password" placeholder="" />
		</div>
	</div>

	<div class="form-group">
		<label for="role_id" class="col-sm-3 control-label">Role:</label>
		<div class="col-sm-8">
			<select class="form-control" id="role_id" name="role_id">
				@foreach ($roles as $role)
					<option value="{{ $role->id }}">{{ $role->display_name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Create</button>
		</div>
	</div>
</form>