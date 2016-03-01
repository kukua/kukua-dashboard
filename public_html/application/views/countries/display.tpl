{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-2 col-sm-offset-2">
				<div style="margin-top: 30px;"></div>
				<a href="/countries/">&laquo; Go back</a>
			</div>
			<div class="col-sm-4">
				<h1 class="text-center">Available data for {$country->getName()}</h1>
				{include file="global/notification.tpl"}
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<form method="post" action="/countries/add_column/{$country->getId()}">
					<table class="table">
						<thead>
							<tr>
								<th class="text-center">Visible</th>
								<th>Name</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							{foreach $columns as $column}
								<tr>
									<td class="text-center">
										{if $column->getVisible() == 1}
											<a href="/countries/toggle_column/{$column->getId()}/0"><i class="glyphicon glyphicon-ok-circle"></i></a>
										{else}
											<a href="/countries/toggle_column/{$column->getId()}/1" class="text-warning"><i class="glyphicon glyphicon-remove-circle"></i></a>
										{/if}
									</td>
									<td>{$column->getName()}</td>
									<td class="text-right">
										<a href="/countries/delete_column/{$column->getId()}/{$country->getId()}" class="text-danger"><i class="glyphicon glyphicon-remove"></i></a>
									</td>
								</tr>
							{/foreach}
							<tr>
								<td class="text-center"><input type="checkbox" name="visible" value="1"></td>
								<td><input type="text" name="name" class="form-control" ></td>
								<td><button type="submit" class="btn btn-primary btn-block">Add</button></td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
{/block}
