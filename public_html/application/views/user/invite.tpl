{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<form method="post" action="{$baseUrl}user/invite">
			<div class="row">
				<div class="col-xs-6 col-xs-offset-3">
					<div class="login">
						<h1 class="text-center">Let's invite!</h1>
						{include file="global/notification.tpl"}
						<input type="text" name="first_name" placeholder="First name" class="form-control" id="first-name">
						<input type="text" name="last_name" placeholder="Last name" class="form-control" id="last-name">
						<input type="text" name="email" class="form-control" aria-label="Enter email" placeholder="info@example.cc">
						<select class="form-control" name="country[]" multiple="multiple">
							{foreach $countries as $country}
								<option value="{$country->getId()}">{$country->getName()}</option>
							{/foreach}
						</select>
						<button type="submit" class="btn btn-default btn-block btn-lg">Invite</button>
						<a href="{$baseUrl}/user" class="btn btn-link btn-block">Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>
{/block}
