<div class="row">
	<div class="col-xs-12">
		<hr>
		<h3>Stations</h3>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="row">
			{foreach $regions as $column => $stations}
				<ul class="list-inline col-sm-3">
					<li class="checkbox js-checkbox">
						<input type="checkbox" class="js-check" data-target="{$stations.region->getId()}" id="region-{$stations.region->getId()}"> <label class="label-unstyled u-text-bold" for="region-{$stations.region->getId()}">{$stations.region->getName()}</label>
						<ul class="list-unstyled">
							{foreach $stations.stations as $station}
								{$checked = ""}
								{foreach $userStations as $userStation}
									{if ($userStation->getId() == $station->getId())}
										{$checked = "checked='checked'"}
										{break}
									{/if}
								{/foreach}
								<li><input type="checkbox" name="userStations[]" value="{$station->getId()}" class="js-checkbox-result" data-region_id="{$station->getRegionId()}" {$checked} id="station-{$station->getDeviceId()}"> <label for="station-{$station->getDeviceId()}" class="label-unstyled">{$station->getName()}</label></li>
							{/foreach}
						</ul>
					</li>
				</ul>
			{/foreach}
		</div>
	</div>
</div>
