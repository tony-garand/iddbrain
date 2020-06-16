<br/>
<form id="frm" class="form-horizontal frm" method="post" action="/nodes/add_node">
	{{ csrf_field() }}

	<div class="form-group">
		<label for="repo_name" class="col-sm-3 control-label">Node Name:</label>
		<div class="col-sm-8">
			<input type="text" class="clean form-control" id="node_name" name="node_name" placeholder="node-name" />
			<br/>
			<b>Warning:</b> This will create a new node on digitalocean. Be sure you really want to do this!
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button id="add_btn" type="submit" class="btn btn-primary add_btn">Create</button>
		</div>
	</div>
</form>