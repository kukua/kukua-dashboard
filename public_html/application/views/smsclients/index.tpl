{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>
                    Client SMS Receivers
                    <a href="{$baseUrl}smsclients/create" class="btn btn-primary pull-right">Add client</a>
                </h1>
                {include file="global/notification.tpl"}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-condensed table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Number</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $clients as $client}
                            <tr>
                                <td>{$client->getName()}</td>
                                <td>{$client->getLocation()}</td>
                                <td>{$client->getNumber()}</td>
                                <td>{$client->getCreated()|date_format:"%d-%M-%Y"}
                                <td><a href="/smsclients/delete/{$client->getId()}" data-text="Are you sure you want to remove this client?" class="js-confirm-delete pull-right"><i class="glyphicon glyphicon-remove text-danger"></i></a></td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/block}
