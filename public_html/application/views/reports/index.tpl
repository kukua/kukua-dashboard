{extends file="layout/master.tpl"}

{block name="content"}
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
							<th align='left'>DB Rows</th>
							<th align='left'>Measurement</th>
							<th align='left'>Region</th>
							<th align='left'>Station</th>
							<th align='left'>Date</th>
						</tr>
					</thead>

					<tbody>
						{$class = ""}
						{foreach $reports as $report}
							{if $report->getCount() <= 72 }
								{$class = "bg-red"}
							{elseif $report->getCount() <= 144}
								{$class = "bg-orange"}
							{elseif $report->getCount() <= 216}
								{$class = "bg-blue"}
							{elseif $report->getCount() > 216}
								{$class = "bg-green"}
							{/if}

							<tr>
								<td class="{$class}">{$report->getCount()}</td>
								<td>{$report->getMeasurement()}</td>
								<td>{$report->getRegion()}</td>
								<td>{$report->getStation()}</td>
								<td>{$report->getCreated()|date_format:"%d-%m-%Y %H:%I:%S"}
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
{/block}
