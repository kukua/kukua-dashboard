{extends file="layout/master.tpl"}

{block name="content"}
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="row">
					<div class="col-sm-3"></div>
					<div class="col-sm-9">
						<h1>Add region</h1>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<form method="post" action="{$baseUrl}regions/create/" class="form-horizontal">
							{include file="global/notification.tpl"}
							{include file="regions/form.tpl"}
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
{/block}
