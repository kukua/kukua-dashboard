{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<h1 class="text-center">Available countries</h1>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				{include file="global/notification.tpl"}
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Country</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach $countries as $country}
							<tr class="pointer js-row-link" data-href="/locations/view_country/{$country->getId()}">
								<td>{$country->getName()}</td>
								<td class="text-right">
									<a href="/locations/delete_country/{$country->getId()}" class="text-danger js-confirm-delete" data-text="Are you sure you want to delete this country? If you continue, all the stations connected will also be deleted."><i class="glyphicon glyphicon-remove"></i></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<a class="btn btn-primary" href="/locations/add_country">Add Country</a>
			</div>
		</div>
	</div>
{/block}
