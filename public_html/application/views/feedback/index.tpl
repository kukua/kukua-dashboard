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
                            <td>E-mail</td>
                            <td>Created</td>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $feedback as $fb}
                            <tr>
                                <td>{$fb.first_name} {$fb.last_name}</td>
                                <td>{$fb.feedback}</td>
                                <td>{$fb.email}</td>
                                <td>{$fb.created|date_format:"%d-%m-%Y"}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/block}
