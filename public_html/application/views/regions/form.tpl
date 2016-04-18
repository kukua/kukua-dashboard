<div class="form-group">
	<label for="cityName" class="col-sm-3 control-label">Name</label>
	<div class="col-sm-9">
		<input type="text" name="name" placeholder="City name" class="form-control" id="cityName" value="{$region->getName()}">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3"></label>
	<div class="col-sm-3">
		<button type="submit" class="btn btn-success btn-block">Save</button>
	</div>
	<div class="col-sm-3">
		<a href="{$baseUrl}regions/index/" class="btn btn-link">Cancel</a>
	</div>
</div>
