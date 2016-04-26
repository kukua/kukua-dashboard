{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-12">

				<h1>
					<i class="glyphicon glyphicon-bullhorn"></i> Stations
					<a href="/stations/create/" class="btn btn-primary pull-right">Add station</a>
				</h1>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				{include file="global/notification.tpl"}
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<table class="table js-datatable">
					<thead>
						<th>Name</th>
						<th>Device_id</th>
						<th>SIM ID</th>
						<th>Lat/Long</th>
						<th></th>
					</thead>
					<tbody>
						{if (count($stations))}
							{foreach $stations as $station}
								{$class = ""}
								{if $station->getActive() != 1}
									{$class = "bg-warning"}
								{/if}
								<tr class="{$class}">
									<td>{$station->getName()|ucfirst}</td>
									<td>{$station->getDeviceId()}</td>
									<td>{$station->getSimId()}</td>
									<td>{$station->getLatitude()} / {$station->getLongitude()}</td>
									<td class="text-right">
										{if $station->getActive() == 1}
											<a href="/stations/disable/{$station->getId()}" class="text-info js-confirm-disable" title="Don't display station in chart"><i class="glyphicon glyphicon-eye-close"></i></a>
										{else}
											<a href="/stations/enable/{$station->getId()}" class="text-warning" title="Display station in chart"><i class="glyphicon glyphicon-eye-open"></i></a>
										{/if}
										<a href="/stations/update/{$station->getId()}" class="text-muted"><i class="glyphicon glyphicon-pencil"></i></a>
										<a href="/stations/delete/{$station->getId()}" class="text-danger js-confirm-delete"><i class="glyphicon glyphicon-remove"></i></a>
									</td>
								</tr>
							{/foreach}
						{/if}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<a href="/stations/create/" class="btn btn-primary">Add station</a>
			</div>
		</div>
	</div>
{/block}
