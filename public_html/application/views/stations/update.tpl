{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="col-sm-3"></div>
				<div class="col-sm-9">
					<h1 class="">Updating {$station->getName()}</h1>
				</div>
				<form method="post" action="{$baseUrl}stations/update/{$station->getId()}" class="form-horizontal">
					{include file="global/notification.tpl"}
					{include file="stations/form.tpl"}
				</form>
			</div>
		</div>
	</div>
{/block}
