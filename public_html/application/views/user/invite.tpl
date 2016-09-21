{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<form method="post" action="{$baseUrl}user/invite" class="form-horizontal">
			<div class="row">
				<div class="col-xs-7 col-xs-offset-3">
					<h1 class="">Let's invite!</h1>
					{include file="global/notification.tpl"}
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label for="first-name" class="col-sm-3 control-label">Name</label>
					<div class="col-sm-3">
						<input type="text" name="first_name" placeholder="First name" class="form-control" id="first-name">
					</div>
					<div class="col-sm-4">
						<input type="text" name="last_name" placeholder="Last name" class="form-control" id="last-name">
					</div>
				</div>

				<div class="form-group">
					<label for="email" class="col-sm-3 control-label">Username</label>
					<div class="col-sm-7">
						<input type="text" name="identity" class="form-control" aria-label="Enter username" placeholder="username">
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-3 control-label">E-mail address</label>
					<div class="col-sm-7">
						<input type="text" name="email" class="form-control" aria-label="Enter email" placeholder="info@example.cc">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-7 col-sm-offset-3">
					{include file="user/_userStations.tpl"}
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<hr>
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label class="col-sm-3"></label>
					<div class="col-sm-3">
						<button type="submit" class="btn btn-default btn-block">Invite</button>
					</div>
					<div class="col-sm-3">
						<a href="{$baseUrl}/user" class="btn btn-link btn-block">Cancel</a>
					</div>
				</div>
					<div class="">
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}
