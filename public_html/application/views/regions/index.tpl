{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>
					<i class="glyphicon glyphicon-globe"></i>
					Regions
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
						{if (count($regions))}
							{foreach $regions as $region}
								<tr>
									<td>{$region->getName()|ucfirst}</td>
									<td class="text-right">
										<a href="/regions/update/{$region->getId()}" class="text-muted"><i class="glyphicon glyphicon-pencil"></i></a>
										<!--<a href="/regions/delete/{$region->getId()}" class="text-danger js-confirm-delete"><i class="glyphicon glyphicon-remove"></i></a>-->
									</td>
								</tr>
							{/foreach}
						{/if}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<a href="/regions/create/" class="btn btn-primary">Add region</a>
			</div>
		</div>
	</div>
{/block}
