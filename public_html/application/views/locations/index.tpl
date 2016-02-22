{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="text-center">The countries and stations</h1>
                {include file="global/notification.tpl"}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-4 col-xs-offset-2">
                <label>Select country</label>
                <select name="country" id="js-location-post" class="form-control">
                    {foreach $countries as $country}
                        <option value="{$country->getId()}">{$country->getName()}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-xs-4">
                <form method="post" action="/locations/delete_country">
                    <input type="hidden" name="country_id" class="input-country-id">
                    <label>&nbsp;</label><br>
                    <div class="pull-right">
                        <div class="btn-group">
                            <a class="btn btn-primary" href="/locations/add_country">Add Country</a>
                            <button type="submit" class="btn btn-danger js-confirm-delete" data-text="Are you sure you want to delete this country? If you continue, all the stations connected will also be deleted.">Remove country</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <form method="post" action="/locations/add_station" id="stationForm">
                    <input type="hidden" name="country_id" class="input-country-id">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>City</th>
                                <th>Station ID</th>
                            </tr>
                        </thead>
                        <tbody class="js-table-result"></tbody>
                    </table>
                    <button class="btn btn-success">Add station</a>
                </form>
            </div>
        </div>
    </div>
{/block}
