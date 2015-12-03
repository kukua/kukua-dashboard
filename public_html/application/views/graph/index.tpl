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
                <div class="col-sm-2 col-sm-offset-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Download CSV</a>
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
