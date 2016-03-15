{extends file="layout/master.tpl"}

{block name="content"}
<div class="login_background">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
				<div class="login">
					<h1>Reset password</h1>
                	{include file="global/notification.tpl"}

                	<form class="form-signin" method="post" action="/auth/reset_password/{$code}">
                	    <div>
                	        <label for="new" class="sr-only">New password</label>
                	        <input type="password" name="new" id="new" class="form-control" placeholder="New password">
                	    </div>
                	    <div>
                	        <label for="new_confirm" class="sr-only">Repeat new password</label>
                	        <input type="password" name="new_confirm" id="new_confirm" class="form-control"  placeholder="Repeat new password">
                	    </div>
                	    <div>
                	        <input type="hidden" name="{$csrf.name}" id="csrf" value="{$csrf.value}">
                	        <input type="hidden" name="user_id" value="{$user_id}" id="user_id">
                	        <button class="btn btn-lg btn-default btn-block" type="submit">Reset password</button>
                	    </div>
                	</form>
				</div>
            </div>
        </div>
    </div>
</div>
{/block}
