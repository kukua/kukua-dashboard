{extends file="layout/master.tpl"}

{block name="content"}

	{function getClass level=0}
		{if $data >= 216}
			bg-green
		{elseif ($data >= 144 && $data < 216)}
			bg-blue
		{elseif ($data >= 72 && $data < 144)}
			bg-orange
		{else}
			bg-red
		{/if}
	{/function}

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>
					<i class="glyphicon glyphicon-list-alt"></i>
					Report
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
				<table class="table table-condensed js-datatable">
					<thead>
						<tr>
							<th align='left'>Date</th>
							<th align='left'>Region</th>
							<th align='left'>Station</th>
							<th align='left'>Temp</th>
							<th align='left'>Rain</th>
							<th align='left'>Pressure</th>
							<th align='left'>Humid</th>
							<th align='left'>Solar</th>
							<th align='left'>Wind speed</th>
							<th align='left'>Wind dir</th>
							<th align='left'>Gust speed</th>
							<th align='left'>Gust dir</th>
							<th align='left'>Battery</th>
						</tr>
					</thead>

					<tbody>
						{foreach $reports as $report}
							{assign var=measurement value=$report->getRows()|json_decode:1}

							<tr>
								<td>{$report->getCreated()|date_format:"%d-%m-%Y %H:%I:%S"}
								<td>{$report->getRegion()}</td>
								<td>{$report->getStation()}</td>

								<td class="{getClass data=$measurement.temp}">{$measurement.temp}</td>
								<td class="{getClass data=$measurement.rain}">{$measurement.rain}</td>
								<td class="{getClass data=$measurement.pressure}">{$measurement.pressure}</td>
								<td class="{getClass data=$measurement.humid}">{$measurement.humid}</td>
								<td class="{getClass data=$measurement.solarIrrad}">{$measurement.solarIrrad}</td>
								<td class="{getClass data=$measurement.windSpeed}">{$measurement.windSpeed}</td>
								<td class="{getClass data=$measurement.windDir}">{$measurement.windDir}</td>
								<td class="{getClass data=$measurement.gustSpeed}">{$measurement.gustSpeed}</td>
								<td class="{getClass data=$measurement.gustDir}">{$measurement.gustDir}</td>
								<td class="{getClass data=$measurement.battery}">{$measurement.battery}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
{/block}
