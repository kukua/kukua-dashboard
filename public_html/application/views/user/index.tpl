{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    Users
                    <a href="{$base_url}/user/invite" class="btn btn-primary pull-right">Invite user</a>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                {include file="global/notification.tpl"}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>E-mail address</td>
                            <td>Payed untill</td>
                            <td>Last login</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $users as $user}
                            {$class = ""}
                            {if $user->isAdmin}
                                {$class = "bg-success"}
                            {/if}
                            <tr>
                                <td class="{$class}">{$user->first_name} {$user->last_name}</td>
                                <td class="{$class}">{$user->email}</td>
                                <td class="{$class}">-</td>
                                <td class="{$class}">{$user->last_login|date_format:"%B %e, %Y"}</td>
                                <td class="{$class} text-center">
                                    <p>
                                        {if $user->isAdmin}
                                            <a href="{$base_url}/user/revoke/{$smarty.const.GROUP_ADMIN}/{$user->id}" class="label label-default js-confirm-revoke" title="Revoke admin access">admin</i></a>
                                        {else}
                                            <a href="{$base_url}/user/grant/{$smarty.const.GROUP_ADMIN}/{$user->id}" class="text-danger js-confirm-grant" title="Grant admin access"><i class="glyphicon glyphicon-ok-circle"></i></a>
                                        {/if}
                                    </p>
                                </td>
                                <td class="{$class} text-right">
                                    <a href="{$base_url}/user/update/{$user->id}"><i class="glyphicon glyphicon-pencil"></i></a>
                                    <a href="{$base_url}/user/delete/{$user->id}" class="text-muted js-confirm-delete"><i class="glyphicon glyphicon-remove"></i></a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/block}
