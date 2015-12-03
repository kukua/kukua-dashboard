{extends file="layout/master.tpl"}

{block name=content}
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <a href="/graph" class="btn btn-primary btn-block" title="Go to the weather per location overview">Weather per location</a><br/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <a href="/forecast" class="btn btn-primary btn-block" title="Go to the weather forecast">Forecast</a><br/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <a href="/auth/logout" id="logout" class="btn btn-primary btn-block" title="Log out of dashboard">Log out</a><br/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <a href="http://www.kukua.cc" title="Go to the Kukua homepage" target="_blank">www.kukua.cc</a>
            </div>
        </div>
    </div>
{/block}
