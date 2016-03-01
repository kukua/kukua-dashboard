{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
			<div class="col-sm-2 col-sm-offset-2">
				<div style="margin-top: 30px;"></div>
				<a href="/stations/index/{$station->getCountryId()}">&laquo; Go back</a>
			</div>
			<div class="col-sm-4">
				<h1 class="text-center">{$station->getName()|ucfirst}<br> <small>{$station->getStationId()}</small></h1>
			</div>
        </div>
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
				{include file="global/notification.tpl"}
                <form method="post" action="/stations/add_station_column/{$station->getId()}">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Value</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $columns as $column}
                                <tr>
                                    <td>
										{foreach $availableColumns as $countryColumn}
											{if $column->getCountryColumnId() == $countryColumn->getId()}
												{$countryColumn->getName()}
												{break}
											{/if}
										{/foreach}
									</td>
                                    <td>{$column->getValue()}</td>
                                    <td><a href="/stations/delete_station_column/{$column->getId()}/{$station->getId()}" class="text-danger pull-right"><i class="glyphicon glyphicon-remove"></i></a></td>
                                </tr>
                            {/foreach}
                            <tr>
                                <td>
                                    <select id="js-graph-type-swap" class="form-control" name="country_column_id">
                                        {foreach $availableColumns as $column}
                                            <option value="{$column->getId()}">{$column->getName()}</option>
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
