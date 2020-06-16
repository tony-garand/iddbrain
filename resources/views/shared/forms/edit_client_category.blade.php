<br/>
<form id="frm" class="form-horizontal" method="post" action="/tools/client-categories/update/{{ $category->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Title:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="title" name="title" placeholder="" value="{{ $category->title ?? old('title') }}">
		</div>
	</div>
	<div class="form-group">
		<label for="color" class="col-sm-3 control-label">Color:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="color" name="color" placeholder="" value="{{ $category->color ?? old('color') }}">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-8 text-right">
			<button id="add_btn" type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>