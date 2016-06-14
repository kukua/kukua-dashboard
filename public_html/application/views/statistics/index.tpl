{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1><i class="glyphicon glyphicon-stats"></i> Statistics</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table class="table js-datatable table-striped table-condensed">
					<thead>
						<tr>
							<th>Name</th>
							<th>ICCID</th>
							<th width="80px" class="text-center">Buffers</th>
							<th width="80px" class="text-center">Records</th>
							<th width="150px" class="text-center">Last DB TS</th>
							<th width="80px" class="text-center">Voltage</th>
							<th width="150px" class="text-center">Connection</th>
						</tr>
					</thead>
					<tbody>
						{if count($simcards)}
							{foreach $simcards as $card}
								<tr>
									<td>{$card->name}</td>
									<td>{$card->ICCID}</td>
									<td class="text-center">NYI</td>
									<td class="text-center">NYI</td>
									<td class="text-center">{$card->timestamp}</td>
									<td class="text-center bg-{$card->voltageColor}"><a href="/graph?region={$card->regionId}&graph=battery" target="_blank" class="text-black">{$card->voltage}</a></td>
									<td class="text-center bg-{$card->statusColor}">{$card->status}</td>
								</tr>
							{/foreach}
						{/if}
					</tbody>
				</table>
			</div>
		</div>
	</div>
{/block}
