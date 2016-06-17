{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="col-sm-3"></div>
				<div class="col-sm-9">
					<h1 class="">Update measurement</h1>
				</div>
				<form method="post" action="{$baseUrl}stationdetails/update/{$measurement->getId()}" class="form-horizontal">
					{include file="global/notification.tpl"}
					{include file="stationdetails/form.tpl"}
				</form>
			</div>
		</div>
	</div>
{/block}
