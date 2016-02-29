{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
				<div class="login">
					{include file="global/notification.tpl"}
                	<form method="post" action="{$baseUrl}auth/activate/{$user->activation_code}">
                	    <div class="form-group">
                	        <label for="">E-mail address</label>
                	        <input type="text" value="{$user->email}" disabled="disabled" class="form-control">
                	    </div>

                	    <div class="form-group">
                	        <label for="first-name">Name</label>
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
                	        <div class="row">
                	            <div class="col-xs-12">
                	            </div>
                	            <div class="col-xs-6">
                	                <label for="password">Password</label>
                	                <input type="password" name="password" id="password" class="form-control">
                	            </div>
                	            <div class="col-xs-6">
                	                <label for="password_confirm">Repeat password</label>
                	                <input type="password" name="password_confirm" id="password_confirm" class="form-control">
                	            </div>
                	        </div>
                	    </div>

                	    <div class="form-group">
                	        <button type="submit" class="btn btn-success btn-block btn-lg">Register</button>
                	    </div>
                	</form>
				</div>
            </div>
        </div>
    </div>
{/block}
