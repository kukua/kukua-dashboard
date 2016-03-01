{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
				<div class="login">
					<h1 class="text-center">Add a country</h1>
					{include file="global/notification.tpl"}
					<form method="post" action="{$baseUrl}countries/create">
						<label for="countryName" class="sr-only">Country code</label>
                		<input type="text" name="code" placeholder="Country code" class="form-control input-lg" id="countryCode">
						<label for="countryName" class="sr-only">Country name</label>
                		<input type="text" name="name" placeholder="Country name" class="form-control input-lg" id="countryName">

						<button type="submit" class="btn btn-success btn-block btn-lg">Add country</button>
                		<a href="{$baseUrl}countries" class="btn btn-link btn-block">Cancel</a>
					</form>
				</div>
			</div>
        </div>
    </div>
{/block}
