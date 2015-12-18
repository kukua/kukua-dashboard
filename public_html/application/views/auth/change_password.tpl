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
                <h1>Change password</h1>
                {include file="global/notification.tpl"}

                <form class="" method="post" action="/auth/change_password">
                    <div>
                        <label for="old">Old password</label>
                        <input type="password" name="old" id="old">
                    </div>

                    <div>
                        <label for="new">New password</label>
                        <input type="password" name="new" id="new">
                    </div>

                    <div>
                        <label for="new_confirm">Repeat new password</label>
                        <input type="password" name="new_confirm" id="new_confirm">
                    </div>
                    <div>
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Change password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/block}
