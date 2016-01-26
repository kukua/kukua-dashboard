{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <form method="post" action="{$baseUrl}user/invite">
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <h1 class="text-center">Let's invite!</h1>
                    {include file="global/notification.tpl"}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <div class="input-group">
                        <input type="text" name="first_name" placeholder="First name" class="form-control input-lg input-group-custom" id="first-name">
                        <span class="input-group-btn input-group-btn-none"></span>
                        <input type="text" name="last_name" placeholder="Last name" class="form-control input-lg input-group-custom input-group-custom-outer" id="last-name">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <input type="text" name="email" class="form-control input-lg" aria-label="Enter email" placeholder="info@example.cc">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <select class="form-control" name="country[]" multiple="multiple">
                        {foreach GlobalHelper::getCountries() as $key => $country}
                            <option value="{$key}">{$country}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-3">
                    <button type="submit" class="btn btn-success btn-block btn-lg">Invite</button>
                    <a href="{$baseUrl}/user" class="btn btn-link btn-block">Cancel</a>
                </div>
            </div>
        </form>
    </div>
{/block}
