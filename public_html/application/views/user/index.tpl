{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1>
					<i class="glyphicon glyphicon-user"></i>
					Users
					<a href="{$baseUrl}user/invite" class="btn btn-primary pull-right">Invite user</a>
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
				<table class="table table-striped js-datatable">
					<thead>
						<tr>
							<td>Name</td>
							<td>E-mail address</td>
							<td>Last login</td>
							<td>Group</td>
							<td></td>
						</tr>
					</thead>
					<tbody>
						{foreach $users as $user}
							{$class = ""}
							{if $user->isAdmin}
								{$class = "bg-success"}
							{elseif $user->isManager}
								{$class = "bg-info"}
							{/if}
							{if $user->active == "0"}
								{$class = "bg-info"}
							{/if}
							<tr>
								<td class="{$class}">{$user->first_name} {$user->last_name}</td>
								<td class="{$class}">{$user->email}</td>
								<td class="{$class}">{$user->last_login|date_format:"%B %e, %Y"}</td>
								<td class="{$class}">
									<p>
										{if $user->isAdmin}
											<span class="label label-default" style="position:relative;top:3px;">admin</span>
										{elseif $user->isManager}
											<span class="label label-default" style="position:relative;top:3px;">manager</span>
										{/if}
									</p>
								</td>
								<td class="{$class} text-right">
									{if !empty($user->activation_code)}
										<a href="{$baseUrl}user/resendInvite/{$user->id}" class="btn btn-primary btn-xs">(re) send invite</a>
									{/if}
									{if $user->active == "1"}
										<a href="{$baseUrl}user/disable/{$user->id}" title="Disable this account"><i class="text-muted glyphicon glyphicon-eye-close"></i></a>
									{else}
										<a href="{$baseUrl}user/enable/{$user->id}" title="Enable this account"><i class="text-success glyphicon glyphicon-eye-open"></i></a>
									{/if}
									<a href="{$baseUrl}user/update/{$user->id}"><i class="text-warning glyphicon glyphicon-pencil"></i></a>
									<a href="{$baseUrl}user/delete/{$user->id}" class="text-danger js-confirm-delete"><i class="glyphicon glyphicon-remove"></i></a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<a href="{$baseUrl}user/invite" class="btn btn-primary">Invite user</a>
			</div>
		</div>
	</div>
{/block}
