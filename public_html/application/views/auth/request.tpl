{extends file="layout/master.tpl"}

{block name="content"}
	<div class="login_background">
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-sm-offset-4">
					<div class="login">
						<h1 class="ellipsis">Request access</h1>
						{include file="global/notification.tpl"}
						<form class="form-signin clearfix" method="post" action="/auth/request">
							<div>
								<label for="inputFirstname" class="sr-only">First name</label>
								<input type="text" id="inputFirstname" name="first_name" class="form-control" placeholder="First name">
							</div>
							<div>
								<label for="inputLastname" class="sr-only">Last name</label>
								<input type="text" id="inputLastname" name="last_name" class="form-control" placeholder="Last name">
							</div>
							<div>
								<label for="inputIdentity" class="sr-only">Username</label>
								<input type="text" id="inputIdentity" name="identity" class="form-control" placeholder="Username">
							</div>
							<div>
								<label for="inputEmail" class="sr-only">E-mail address</label>
								<input type="text" id="inputEmail" name="email" class="form-control" placeholder="E-mail address">
							</div>
							<div class="">
								<button class="btn btn-lg btn-default btn-block" type="submit">Request access</button>
							</div>
						</form>

						<a href="http://www.kukua.cc" title="Go to the Kukua homepage" target="_blank">www.kukua.cc</a>
					</div>
				</div>
			</div>
		</div>
	</div>
{/block}
