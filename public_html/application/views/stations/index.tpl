{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-2 col-sm-offset-2">
				<div style="margin-top: 30px;"></div>
				<a href="/countries/">&laquo; Go back</a>
			</div>
			<div class="col-sm-4">
				<h1 class="text-center">Stations in {$country->getName()}</h1>
				{include file="global/notification.tpl"}
			</div>
		</div>

		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<table class="table">
					<thead>
						<th>Name</th>
						<th>Copy params from...</th>
						<th></th>
						<th></th>
					</thead>
					<tbody>
						{foreach $stations as $station}
							{$class = ""}
							{if $station->getActive() != 1}
								{$class = "bg-warning"}
							{/if}
							<form method="post" action="/stations/copyParams/{$station->getId()}/{$country->getId()}">
								<tr class="{$class}">
									<td>{$station->getName()|ucfirst}</td>
									{if count($stations) > 1}
										<td>
											<select class="js-station-copy form-control" name="copyFrom">
												{foreach $stations as $copyStation}
													{if $copyStation->getId() != $station->getId()}
														<option value="{$copyStation->getId()}">{$copyStation->getName()|ucfirst}</option>
													{/if}
												{/foreach}
											</select>
										</td>
										<td>
											<button type="submit" class="btn btn-primary">Copy</button>
										</td>
									{else}
										<td class="text-muted">Nothing to copy from yet</td>
									{/if}
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
							</form>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<a href="/stations/create/{$country->getId()}" class="btn btn-primary">Add station</a>
			</div>
		</div>
	</div>
{/block}
