{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <form method="post" action="{$baseUrl}locations/add_station">
            <input type="hidden" name="country_id" value="{$country->id}">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <h1 class="text-center">Add a station to {$country->name}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="form-group">
                        <label for="stationId">Station ID (influxDB WHERE)</label>
                        <input type="text" name="station_id" placeholder="Station id" class="form-control input-lg" id="stationId">
                    </div>
                    <div class="form-group">
                        <label for="cityName">Name</label>
                        <input type="text" name="name" placeholder="City name" class="form-control input-lg" id="cityName">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <button type="submit" class="btn btn-success btn-block btn-lg">Add station</button>
                    <a href="{$baseUrl}locations" class="btn btn-link btn-block">Cancel</a>
                </div>
            </div>
        </form>
    </div>
{/block}
