{extends file="layout/master.tpl"}

{block name="content"}
	<div class="advanced js-advanced-container">
		<div class="container">
			<form class="form" action="/graph/download" id="js-submit" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded" target="_blank">
				<div class="row">
					<div class="col-sm-2">
						<label class="ellipsis">Stations</label>
						<select class="form-control" id="js-graph-station" name="station">
							{foreach $stations as $station}
								<option value="{$station->getId()}">{$station->getName()}</option>
							{/foreach}
						</select>
					</div>
					<div class="col-sm-2">
						<label class="ellipsis" title="Select graph">Select graph</label>
						<select id='js-graph-type-swap' class="form-control" name="type">
						</select>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="ellipsis" title="Select date range">Date range</label>
							<div id="reportrange" class="ellipsis clearfix" style="background: #fff; cursor: pointer; padding: 6px 10px; border: 1px solid #ccc; width: 100%; border-radius: 4px">
								<i class="glyphicon glyphicon-calendar"></i>&nbsp;
								<span></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group clearfix">
							<label class="ellipsis" title="Display per">Display per</label>
							<select id="js-graph-show-per" class="form-control" name="interval">
								<option value="5m">5 minutes</option>
								<option value="1h">1 hour</option>
								<option value="12h">12 hour</option>
								<option value="24h">24 hour</option>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>&nbsp;</label><br>
							<input type="hidden" name="from" id="dateFrom">
							<input type="hidden" name="to" id="dateTo">
							<button type="submit"class="btn btn-default btn-block ellipsis" title="Download CSV">Download CSV</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="container">
		<div id="sensor-chart" style="width:100%; height:78%;"></div>
	</div>
{/block}
