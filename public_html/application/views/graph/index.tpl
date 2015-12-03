{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        {include file="global/notification.tpl"}

        <form class="form" action="/graph/download" method="post" accept-charset="utf-8" enctype="application/x-www-form-urlencoded">
            <div class="row">
                <div class="col-sm-2">
                    <label>Select location</label>
                    <select id="js-graph-location-swap" class="form-control" name="nation">
                        <option value="">Nationwide</option>
                        {foreach $locations as $location => $stationId}
                            {$selected=""}
                            {if (isset($postLocation) && $location == $postLocation)}
                                {$selected='selected="selected"'}
                            {/if}
                            <option value="{$location}" {$selected}>{$location|ucfirst}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Select graph</label>
                    <select id="js-graph-type-swap" class="form-control" name="panelId">
                        {foreach $panelGraphs as $id => $graph}
                            <option value="{$id}">{$graph}</option>
                        {/foreach}
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
                        <label class="pull-right">Download and group per</label>
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
            <div class="col-sm-3 col-sm-offset-9 u-text-right">
                <small class="">
                    <i class="glyphicon glyphicon-info-sign"></i>
                    Tip: You can enable and disable multiple stations by holding shift or control in the legend below.
                </small>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <iframe
                    id="js-graph"
                    src="{$graphUrl}"
                    width="100%"
                    height="500"
                    frameborder="0"
                    scrolling="no"
                    data-user="{GlobalHelper::getUser()}"
                ></iframe>
            </div>
        </div>
    </div>
{/block}
