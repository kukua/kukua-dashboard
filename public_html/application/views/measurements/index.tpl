{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<a href="/stations" class="btn btn-link"><i class="glyphicon glyphicon-arrow-left"></i> Back to stations</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<h1>
					<i class="glyphicon glyphicon-cog"></i>
					Measurements
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
				<table class="table">
					<thead>
						<th>Name</th>
						<th></th>
					</thead>
					<tbody>
						{if (count($measurements))}
							{foreach $measurements as $measurement}
								<tr>
									<td>{$measurement->getName()}</td>
									<td>{$measurement->getColumn()}</td>
									<td class="text-right">
										<a href="/measurements/update/{$measurement->getId()}" class="text-muted"><i class="glyphicon glyphicon-pencil"></i></a>
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
				<a href="/measurements/create/{$stationId}" class="btn btn-primary">Add measurement</a>
			</div>
		</div>
	</div>
{/block}
