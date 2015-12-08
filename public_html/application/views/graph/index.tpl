{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        {include file="global/notification.tpl"}

        <form class="form" action="/graph/download" id="js-submit" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded">
            <div class="row">
                <div class="col-sm-4">
                    <label>Select graph</label>
                    <select id="js-graph-type-swap" class="form-control" name="panelId">
                        {foreach $panelGraphs as $id => $graph}
                            <option value="{$id}">{$graph}</option>
                        {/foreach}
                        <option value="temp">Temperature</option>
                        <option value="rain">Rainfall</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>From</label>
                            {$value=""}
                            {if isset($postDateFrom)}
                                {$value=$postDateFrom}
                            {else}
                                {$value=GlobalHelper::getDefaultDate("P8D")}
                            {/if}
                        <input type="text" class="form-control" name="from" placeholder="Select a date" id="js-datetimepicker-min" value="{$value}">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>To</label>
                            {$value=""}
                            {if isset($postDateTo)}
                                {$value=$postDateTo}
                            {else}
                                {$value=GlobalHelper::getDefaultDate("P1D")}
                            {/if}
                        <input type="text" class="form-control" name="to" placeholder="Select a date" id="js-datetimepicker-max" value="{$value}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group clearfix">
                        <label class="pull-right">Download csv and group per</label>
                        <div class="clearfix"></div>
                        <div class="btn-group pull-right" role="group">
                            <button type="submit" name="submit" value="5m" class="btn btn-primary">5m</button>
                            <button type="submit" name="submit" value="1h" class="btn btn-primary">1h</button>
                            <button type="submit" name="submit" value="12h" class="btn btn-primary">12h</button>
                            <button type="submit" name="submit" value="24h" class="btn btn-primary">24h</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-xs-12">
                <div id="chart" style="width:100%; height:500px;"></div>
            </div>
        </div>
    </div>
{/block}
