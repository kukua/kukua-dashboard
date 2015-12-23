{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    Feedback
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
                            <td>User</td>
                            <td>Feedback</td>
                            <td>Invitee</td>
                            <td>Created</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $feedback as $fb}
                            {$class = ""}
                            {if ($fb.completed == 1)}
                                {$class = "bg-success"}
                            {/if}
                            <tr>
                                <td class="{$class}">{$fb.first_name} {$fb.last_name}</td>
                                <td class="{$class}">{$fb.feedback}</td>
                                <td class="{$class}">{$fb.email}</td>
                                <td class="{$class}">{$fb.created|date_format:"%d-%m-%Y"}</td>
                                <td class="{$class} text-right">
                                    {if $fb.completed == 1}
                                        <a href="/feedback/uncomplete/{$fb.id}" title="Mark as undone"><i class="glyphicon glyphicon-heart-empty"></i></a>
                                    {else}
                                        <a href="/feedback/complete/{$fb.id}" title="Mark as done"><i class="glyphicon glyphicon-heart"></i></a>
                                    {/if}
                                    <a href="/feedback/delete/{$fb.id}" title="Remove feedback" class="text-danger js-confirm-delete"><i class="glyphicon glyphicon-remove"></i></a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/block}
