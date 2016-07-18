{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1><i class="glyphicon glyphicon-stats"></i> Statistics</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-4">
				Voltage colors
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td class="text-center bg-green">> 4000</td>
							<td class="text-center bg-orange">< 4000</td>
							<td class="text-center bg-red">< 3500</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-xs-4 col-xs-offset-4">
				Date difference colors
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td class="text-center bg-green">< 1</td>
							<td class="text-center bg-blue">> 1 </td>
							<td class="text-center bg-yellow">> 24</td>
							<td class="text-center bg-orange">> 48</td>
							<td class="text-center bg-red">> 96</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table class="table js-statistics-datatable table-striped table-condensed">
					<thead>
						<tr>
							<th>Name</th>
							<th>ICCID</th>
							<th width="150px" class="text-center">Last DB TS</th>
							<th width="80px" class="text-center">Voltage</th>
							<th width="150px" class="text-center">ESEYE Conn</th>
							<th width="" class="text-center">Board temp</th>
							<th width="" class="text-center">Humidity</th>
							<th width="" class="text-center">Light</th>
							<th width="" class="text-center">SigQual</th>
							<th width="" class="text-center">SigQualTime</th>
							<th width="" class="text-center">Link</th>
						</tr>
					</thead>
					<tbody>
						{if count($simcards)}
							{foreach $simcards as $card}
								<tr>
									<td>{$card->name}</td>
									<td>{$card->ICCID}</td>
									<td class="text-center bg-{$card->timestampColor}">{$card->timestamp}</td>
									<td class="text-center bg-{$card->voltageColor}"><a href="/graph?region={$card->regionId}&graph=battery" target="_blank" class="text-black">{$card->voltage}</a></td>
									<td class="text-center bg-{$card->statusColor}">{$card->status}</td>
									<td class="text-center bg-{$card->boardTempColor}">{$card->boardTemp}</td>
									<td class="text-center bg-{$card->boardHumidColor}">{$card->boardHumid}</td>
									<td class="text-center bg-{$card->lightColor}">{$card->light}</td>
									<td class="text-center bg-{$card->sigQualColor}">{$card->sigQual}</td>
									<td class="text-center bg-{$card->sigQualTimeColor}">{$card->sigQualTime}</td>
									<td class="text-center"><a href="{$card->link}" target="_blank">{$card->link}</a></td>
								</tr>
							{/foreach}
						{/if}
					</tbody>
				</table>
			</div>
		</div>
	</div>
{/block}
