{extends file="layout/master.tpl"}

{block name="content"}
	<div class="advanced js-advanced-container">
		<div class="container">
			<form class="form" action="/graph/download" id="js-submit" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded" target="_blank">

				<div class="js-test"></div>

				<div class="row">
					{if count($userCountries) > 1}
						<div class="col-sm-2">
							<label class="ellipsis" title="Select country">Select country</label>
							<select name="country" id="js-graph-country" class="form-control">
								{foreach $userCountries as $object}
									<option value="{$object.country->getId()}">{$object.country->getName()}</option>
								{/foreach}
							</select>
						</div>
					{else}
						<input type="hidden" name="country" id="js-graph-country" class="hidden" value="{$userCountries.0.country->getId()}">
					{/if}
					<div class="col-sm-3">
						<label class="ellipsis" title="Select graph">Select graph</label>
						<select id="js-graph-type-swap" class="form-control" name="panelId"></select>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="ellipsis" title="Select date range">Date range</label>
							<div id="reportrange" class="clearfix" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
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
					<div class="col-sm-1 pull-right">
						<div class="form-group">
							<label>&nbsp;</label><br>
							<input type="hidden" name="from" id="dateFrom">
							<input type="hidden" name="to" id="dateTo">
							<button type="submit"class="btn btn-default" title="Download CSV"><i class="glyphicon glyphicon-download"></i></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<center>
		<button class="advanced-button js-advanced-button btn">
			Filters &amp; Download
			<span class="caret"></span>
		</button>
	</center>

	<div class="container">
		<div id="chart" style="width:100%; height:78%;"></div>

		<div id="chart-forecast"></div>
	</div>
{/block}
