{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-2 col-sm-offset-2">
				<div style="margin-top: 30px;"></div>
				<a href="/locations">&laquo; Go back</a>
			</div>
			<div class="col-sm-4">
				<h1 class="text-center">Stations in {$country->getName()}</h1>
				{include file="global/notification.tpl"}
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<table class="table table-hover">
					<thead>
						<th>Name</th>
						<th></th>
					</thead>
					<tbody>
						{foreach $stations as $station}
							{$class = ""}
							{if $station->getActive() != 1}
								{$class = "bg-warning"}
							{/if}
							<tr class="js-row-link pointer {$class}" data-href="/locations/edit_station/{$station->getId()}">
								<td>{$station->getName()|ucfirst}</td>
								<td class="text-right">
									{if $station->getActive() == 1}
										<a href="/locations/disable_station/{$station->getId()}" class="text-info js-confirm-disable" title="Don't display station in chart"><i class="glyphicon glyphicon-eye-close"></i></a>
									{else}
										<a href="/locations/enable_station/{$station->getId()}" class="text-warning" title="Display station in chart"><i class="glyphicon glyphicon-eye-open"></i></a>
									{/if}
									<a href="/locations/delete_station/{$station->getId()}" class="text-danger js-confirm-delete"><i class="glyphicon glyphicon-remove"></i></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<a href="/locations/add_station/{$country->getId()}" class="btn btn-primary">Add station</a>
			</div>
		</div>
	</div>
{/block}
