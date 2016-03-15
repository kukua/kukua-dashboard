{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-sm-offset-4">
				<div class="login">
					<h1 class="ellipsis">Authenticate</h1>
					{include file="global/notification.tpl"}
					<form class="form-signin" method="post" action="/auth/login">
						<label for="inputEmail" class="sr-only">Emailaddress</label>
						<input type="text" id="inputEmail" name="identity" class="form-control" placeholder="E-mail address">
						<span id="suggestion"></span>
						<div>
							<label for="inputPassword" class="sr-only">Password</label>
							<input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password">
						</div>
						<div class="">
							<button class="btn btn-lg btn-default btn-block" type="submit">Sign in</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4 col-sm-offset-4">
				<a href="http://www.kukua.cc" title="Go to the Kukua homepage" target="_blank">www.kukua.cc</a>
				<a href="/auth/forgot_password" class="pull-right">forgot password?</a>
			</div>
		</div>
	</div>
{/block}
