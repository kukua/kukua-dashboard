<div class="form-group">
	<label for="cityName" class="col-sm-3 control-label">Name</label>
	<div class="col-sm-9">
		<input type="text" name="name" placeholder="City name" class="form-control" id="cityName" value="{$station->getName()}">
	</div>
</div>

<div class="form-group">
	<label for="deviceId" class="col-sm-3 control-label">Device ID</label>
	<div class="col-sm-9">
		<input type="text" name="device_id" placeholder="Device ID" class="form-control" id="deviceId" value="{$station->getDeviceId()}">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3 control-label" for="simId">Sim ID</label>
	<div class="col-sm-9">
		<input type="text" class="form-control" placeholder="Sim ID" name="sim_id" id="simId" value="{$station->getSimId()}">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3 control-label">Position</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" placeholder="Latitude" name="latitude" value="{$station->getLatitude()}">
	</div>
	<div class="col-sm-3">
		<input type="text" class="form-control" placeholder="Longitude" name="longitude" value="{$station->getLongitude()}">
	</div>
	<div class="col-sm-3">
		<input type="text" class="form-control" placeholder="Elevation" name="elevation" value="{$station->getElevation()}">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3 control-label">Region</label>
	<div class="col-sm-9">
		<select name="region_id" class="form-control">
			<option value="-1" disabled='disabled'>Select a region</option>
			{foreach $regions as $region}
				{$selected = ""}
				{if $station->getRegionId() == $region->getId()}
					{$selected = "selected='selected'"}
				{/if}
				<option value="{$region->getId()}" {$selected}>{$region->getName()}</option>
			{/foreach}
		</select>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3"></label>
	<div class="col-sm-3">
		<button type="submit" class="btn btn-success btn-block">Save</button>
	</div>
	<div class="col-sm-3">
		<a href="{$baseUrl}stations/index/" class="btn btn-link">Cancel</a>
	</div>
</div>
