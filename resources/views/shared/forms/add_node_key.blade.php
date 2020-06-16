<br/>
<form id="frm" class="form-horizontal" method="post" action="/nodes/add_key">
{{ csrf_field() }}
<input type="hidden" id="node_ip" name="node_ip" value="" />

	<div class="form-group">
		<label for="key_content" class="col-sm-3 control-label">Public Key Content:</label>
		<div class="col-sm-8">
			<textarea rows="5" class="form-control" id="key_content" name="key_content" placeholder="ssh-rsa AAA..."></textarea>
			<br/>
			<b>Warning:</b> This will create key access for this server. Only do this if you know what you are doing!
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary">Add</button>
			<button data-dismiss="modal" id="cancel_btn" type="button" class="btn btn-cancel">Cancel</button>
		</div>
	</div>
</form>