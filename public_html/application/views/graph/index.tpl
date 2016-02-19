{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        {include file="global/notification.tpl"}

        <form class="form" action="/graph/download" id="js-submit" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded" target="_blank">

            <div class="js-test"></div>

            {if count($userCountries) > 1}
                <div class="row">
                    <div class="col-sm-4">
                        <label>Select country</label>
                        <select name="country" id="js-graph-country" class="form-control">
                            {foreach $userCountries as $object}
                                <option value="{$object.country->id}">{$object.country->name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            {else}
                <input type="hidden" name="country" id="js-graph-country" class="hidden" value="{$userCountries.0.country->id}">
            {/if}
            <div class="row">
                <div class="col-sm-4">
                    <label>Select graph</label>
                    <select id="js-graph-type-swap" class="form-control" name="panelId">
                        {foreach GlobalHelper::graphWeathertypes() as $key => $value}
                            <option value="{$key}">{$value}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Date range</label>
                        <div id="reportrange" class="clearfix" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <div class="pull-left">
                                <i class="glyphicon glyphicon-calendar"></i>&nbsp;
                                <span></span>
                            </div>
                            <div class="pull-right">
                                &nbsp;<i class="glyphicon glyphicon-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group clearfix">
                        <label>Display per</label>
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
                        <label>&nbsp;</label>
                        <input type="hidden" name="from" id="dateFrom">
                        <input type="hidden" name="to" id="dateTo">
                        <input type="submit" value="Download csv" class="btn btn-primary btn-block">
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-xs-12">
                <h3>History, plus one day hourly forecast</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div id="chart" style="width:100%; height:500px;"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <h3 class="js-chart-forecast-title">Ten days forecast</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div id="chart-forecast"></div>
            </div>
        </div>
    </div>
{/block}
