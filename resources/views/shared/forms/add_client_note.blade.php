<br/>
<form id="frm" class="form-horizontal client-credential-form" method="post" action="/client-notes/save/{{ Auth::id() ?? '' }}">
	{{ csrf_field() }}
	<input type="hidden" name="client_id" value="{{ $client->id }}">

	<div class="form-group">
		<label for="color" class="col-sm-3 control-label">Note:</label>
		<div class="col-sm-8">
			<textarea class="form-control" id="value" name="note" rows="5" placeholder="">{{ old('note') }}</textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-8">
			<button id="add_btn" type="submit" class="btn btn-primary">Add New Note</button>
		</div>
	</div>
</form>

<section class="note-list">

	@if(count($notes))
	<div class="msg-box alert"></div>
	<div class="table-responsive">
		<br><br>
		<h4>Notes</h4>
		<table class="table">
			<tbody id="note-list">
			@foreach($notes as $note)
			<tr data-id="{{ $note->id }}">
				<td>
					<label>
						{!! $note->note !!}
					</label>
				</td>
				<td class="text-right">
					<span class="note-time">{{ $note->updated_at }}</span>
				</td>
				<td class="text-right">
					@if(($note->author_id == Auth::id() && $note->manual) || auth()->user()->hasRole('admin'))
					<a class="delete-btn" href="/client-notes/delete/{{ $note->id }}" data-client="{{ $client->id }}" data-id="{{ $note->id }}"><i class="fa fa-close"></i></a>
					@endif
				</td>
			</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	@endif
</section>