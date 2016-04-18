{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="row">
					<div class="col-sm-3"></div>
					<div class="col-sm-9">
						<h1>Update {$region->getName()}</h1>
					</div>
				</div>

				<div class="text-center">
					<form method="post" action="{$baseUrl}regions/update/{$region->getId()}" class="form-horizontal">
						{include file="global/notification.tpl"}
						{include file="regions/form.tpl"}
					</form>
				</div>
			</div>
		</div>
	</div>
{/block}
