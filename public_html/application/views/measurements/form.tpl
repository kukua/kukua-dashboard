<div class="form-group">
	<label for="name" class="control-label col-sm-3">Name</label>
	<div class="col-sm-9">
		<input name="name" class="form-control" placeholder="Measurement name" id="name" value="{$measurement->getName()}">
	</div>
</div>

<div class="form-group">
	<label for="column" class="control-label col-sm-3">Column</label>
	<div class="col-sm-9">
		<input name="column" class="form-control" placeholder="Column name" id="column" value="{$measurement->getColumn()}">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3"></label>
	<div class="col-sm-3">
		<button type="submit" class="btn btn-success btn-block">Save</button>
		<input type="hidden" name="station_id" value="{$stationId}">
	</div>
	<div class="col-sm-3">
		<a href="{$baseUrl}measurements/index/{$stationId}" class="btn btn-link">Cancel</a>
	</div>
</div>
