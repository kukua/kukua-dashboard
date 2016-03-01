{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-sm-offset-4">
				<div class="login">
					<form method="post" action="{$baseUrl}stations/create/{$country->getId()}">
						<h1 class="text-center">Add station to {$country->getName()}</h1>
						{include file="global/notification.tpl"}
						<label for="stationId" class="sr-only">Station ID (influxDB WHERE)</label>
						<input type="text" name="station_id" placeholder="Station id" class="form-control input-lg" id="stationId">
						<label for="cityName" class="sr-only">Name</label>
						<input type="text" name="name" placeholder="City name" class="form-control input-lg" id="cityName">

						<button type="submit" class="btn btn-success btn-block btn-lg">Add station</button>
						<a href="{$baseUrl}stations/index/{$country->getId()}" class="btn btn-link btn-block">Cancel</a>
					</form>
				</div>
			</div>
		</div>
	</div>
{/block}
