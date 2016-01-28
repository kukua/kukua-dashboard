{extends file="layout/master.tpl"}

{block name="content"}
    <form method="post" action="{$baseUrl}locations/add_country">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <h1 class="text-center">Add a country</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-sm-offset-3">
                    <label for="countryName">Name</label>
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="text" name="code" placeholder="Country code" class="form-control input-lg" id="countryCode">
                        </div>
                        <div class="col-sm-8">
                            <input type="text" name="name" placeholder="Country name" class="form-control input-lg" id="countryName">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <button type="submit" class="btn btn-success btn-block btn-lg">Add country</button>
                    <a href="{$baseUrl}locations" class="btn btn-link btn-block">Cancel</a>
                </div>
            </div>
        </div>
    </form>
{/block}
