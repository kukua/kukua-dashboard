{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<h1 class="text-center">Countries</h1>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				{include file="global/notification.tpl"}
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Country</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach $countries as $country}
							<tr>
								<td>{$country->getName()}</td>
								<td class="text-right">
									<a href="/countries/display/{$country->getId()}" title="Chart display data in {$country->getName()}"><i class="glyphicon glyphicon-stats"></i></a>
									<a href="/stations/index/{$country->getId()}" title="Weather stations in {$country->getName()}" class="text-muted"><img src="/assets/img/weatherstation.png"></a>
									<!--<a href="/countries/update/{$country->getId()}" title="Edit country" class="text-muted"><i class="glyphicon glyphicon-pencil"></i></a>-->
									<a href="/countries/delete/{$country->getId()}" title="Delete {$country->getName()}" class="text-danger js-confirm-delete" data-text="Are you sure you want to delete this country? If you continue, all the stations connected will also be deleted."><i class="glyphicon glyphicon-remove"></i></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<a class="btn btn-primary" href="/countries/create">Add Country</a>
			</div>
		</div>
	</div>
{/block}
