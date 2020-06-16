<br/>
<form id="frm" class="form-horizontal" method="post" action="/clients/update/{{ $client->id }}">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="name" class="col-sm-3 control-label">Client Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name" placeholder="Client Name" value="{{ old('name') ?? $client->name }}">
		</div>
	</div>

	<div class="form-group">
		<label for="url" class="col-sm-3 control-label">URL:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="url" name="url" placeholder="www.bobvila.com" value="{{ old('url') ?? $client->url }}">
		</div>
	</div>

	<div class="form-group">
		<label for="description" class="col-sm-3 control-label">Description:</label>
		<div class="col-sm-8">
			<textarea rows="5" class="form-control" id="description" name="description">{{ old('description') ?? $client->description }}</textarea>
		</div>
	</div>

	<div class="form-group">
		<label for="billable_rate" class="col-sm-3 control-label">Billable Rate:</label>
		<div class="col-sm-8">
			<div class="input-group">
				<div class="input-group-addon">$</div>
				<input type="text" class="form-control" id="billable_rate" name="billable_rate" value="{{ old('billable_rate') ?? $client->billable_rate }}">
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="hosting" class="col-sm-3 control-label">Hosting:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="hosting" name="hosting" placeholder="ex. - Digital Ocean" value="{{ old('hosting') ?? $client->hosting }}" />
		</div>
	</div>

	<div class="form-group">
		<label for="categories" class="col-sm-3 control-label">Categories:</label>
		<div class="col-sm-8">
			<div class="select-multiple-results" data-target="#addCategories">
				@foreach($selected_cats as $cat)
					<span style="background-color: {{ $cat->color }}">{{ $cat->title }}<button class="remove-cat" data-id="{{ $cat->id }}"><i class="fa fa-close"></i></button></span>
				@endforeach
			</div>

			<a href="javascript:;" class="btn btn-select" data-toggle="modal" data-target="#addCategories"><i class="fa fa-plus"></i> Select Categories</a>

			<div class="modal fade modal-checkbox-field" id="addCategories" tabindex="-1" role="dialog" aria-labelledby="addCategories">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Select Categories</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								@foreach($categories as $cat)
								<div class="col-sm-3">
									<label class="inline-checkbox">
										<input type="checkbox" name="categories[]" value="{{ $cat->id }}" data-color="{{ $cat->color }}" {{ in_array($cat->id, $cat_ids) ? 'checked' : '' }}> <span style="background-color: {{ $cat->color }}">{{ $cat->title }}</span>
									</label>
								</div>
								@endforeach
							</div>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">Add Categories</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="checkbox">
				<label>
					<input name="is_mm" type="checkbox" {{ $client->is_mm ? 'checked' : '' }}> Message Management?
				</label>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Save</button>
		</div>
	</div>
</form>