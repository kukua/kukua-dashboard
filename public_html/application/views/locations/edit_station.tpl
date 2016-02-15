{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <a href="/locations">go back</a> 
                <h1>
                    Station details {$station->name} <small>{$station->station_id}</small>
                </h1>
                {include file="global/notification.tpl"}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <form method="post" action="/locations/add_station_column/{$station->id}">
                    <table class="table table-condensed table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $columns as $column}
                                <tr>
                                    <td>{$weatherTypes[$column->getKey()]}</td>
                                    <td>{$column->getValue()}</td>
                                    <td><a href="/locations/delete_station_column/{$column->getId()}/{$station->id}" class="text-danger pull-right"><i class="glyphicon glyphicon-remove"></i></a></td>
                                </tr>
                            {/foreach}
                            <tr>
                                <td>
                                    <select id="js-graph-type-swap" class="form-control" name="key">
                                        {foreach $weatherTypes as $key => $value}
                                            <option value="{$key}">{$value}</option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td><input type="text" name="value" placeholder="value" class="form-control"></td>
                                <td><button type="submit" class="btn btn-success btn-block">Add key</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
{/block}
