{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1>
					{$member->first_name} {$member->last_name}
					<a href="{$baseUrl}user/delete/{$member->id}" class="btn btn-danger pull-right"><i class="glyphicon glyphicon-trash"></i> Delete user</a>
					<a href="{$baseUrl}user/disable/{$member->id}" class="btn btn-default pull-right"><i class="glyphicon glyphicon-eye-close"></i> Disable user</a>
				</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				{include file="global/notification.tpl"}
			</div>
		</div>


		<div class="row">
			<div class="col-sm-12">
				<form method="post" action="/user/update/{$member->id}" class="form-horizontal">
					<div class="row">
						<div class="col-sm-6">
							<h4><i class="glyphicon glyphicon-user"></i> Personal info</h4>
						</div>
						<div class="col-sm-6">
							<button type="submit" class="btn btn-primary pull-right">Update user</button>
						</div>
					</div>

					<div class="form-group">
						<label for="email" class="control-label col-sm-3">E-mail address</label>
						<div class="col-sm-6">
							<p class="form-control-static">{$member->email}</p>
						</div>
					</div>
					<div class="form-group">
						<label for="username" class="control-label col-sm-3">Username</label>
						<div class="col-sm-6">
							<input type="text" name="identity" placeholder="Username" class="form-control" id="username" value="{$member->identity}">
						</div>
					</div>

					<div class="form-group">
						<label for="username" class="control-label col-sm-3">Name</label>
						<div class="col-sm-3">
							<input type="text" name="first_name" placeholder="First name" class="form-control" id="first-name" value="{$member->first_name}">
						</div>
						<div class="col-sm-3">
							<input type="text" name="last_name" placeholder="Last name" class="form-control" id="last-name" value="{$member->last_name}">
						</div>
					</div>

					<div class="form-group">
						<label for="password" class="control-label col-sm-3">Password (only if changing)</label>
						<div class="col-sm-3">
							<input type="password" class="form-control" id="new" name="new" placeholder="New password">
						</div>
						<div class="col-sm-3">
							<input type="password" class="form-control" id="new_confirm" name="new_confirm" placeholder="Confirm new password">
						</div>
					</div>

					{if $isAdmin === true}
						{include file="user/_groups.tpl"}
						<hr>
						{include file="user/_userStations.tpl"}
					{/if}

					<div class="form-group">
						<div class="col-sm-9 col-sm-offset-3">
							<button type="submit" class="btn btn-primary">Update user</button>
							<a href="{$baseUrl}user/" class="btn btn-link">Cancel</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
{/block}
