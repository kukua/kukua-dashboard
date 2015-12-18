{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4 u-text-center">
                <img src="/assets/img/kukua-logo-small.png">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <h1>Reset password</h1>
                {include file="global/notification.tpl"}

                <form class="form-signin" method="post" action="/auth/reset_password/{$code}">
                    <div>
                        <label for="new">New password</label>
                        <input type="password" name="new" id="new" class="form-control" autofocus="autofocus">
                    </div>
                    <div>
                        <label for="new_confirm">Repeat new password</label>
                        <input type="password" name="new_confirm" id="new_confirm" class="form-control" >
                    </div>
                    <div>
                        <input type="hidden" name="{$csrf.name}" id="csrf" value="{$csrf.value}">
                        <input type="hidden" name="user_id" value="{$user_id}" id="user_id">
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Reset password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
