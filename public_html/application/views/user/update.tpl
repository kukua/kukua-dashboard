{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <form method="post">
            {include file="global/notification.tpl"}
            <div class="row">
                <div class="col-sm-6">
                    <h4><i class="glyphicon glyphicon-user"></i> Personal info</h4>
                    <hr>
                    <div class="form-group">
                        <label for="email">E-mail address</label>
                        <p class="form-control-static">{$user->email}</p>
                    </div>
                    <div class="form-group">
                        <label for="username">Name</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="text" name="first_name" placeholder="First name" class="form-control" id="first-name" value="{$user->first_name}">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="last_name" placeholder="Last name" class="form-control" id="last-name" value="{$user->last_name}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password (only if changing)</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="new" name="new" placeholder="New password">
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="new_confirm" name="new_confirm" placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h4><i class="glyphicon glyphicon-credit-card"></i> Payment info</h4>
                    <hr>
                    <div class="form-group">
                        <label for="card_number" >Payed untill</label>
                        <p class="form-control-static">N.E.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-4">
                    <div class="">
                        <button type="submit" class="btn btn-primary btn-block">Save</button>
                        <a href="{$base_url}/user/read/{$user->id}" class="btn btn-link btn-block">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/block}
