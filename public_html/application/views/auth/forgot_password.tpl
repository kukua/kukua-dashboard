{extends file="layout/master.tpl"}

{block name="content"}
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4 u-text-center">
                <img src="/assets/img/kukua-logo-small.png">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <h1>Recovery</h1>
                {include file="global/notification.tpl"}

                <form class="form-signin" method="post" action="/auth/forgot_password">
                    <label for="inputEmail" class="sr-only">E-mail address</label>
                    <input type="text" id="inputEmail" name="identity" class="form-control" placeholder="E-mail address" autofocus="autofocus">
                    <span id="suggestion"></span>
                    <div class="">
                        <button class="btn btn-lg btn-primary btn-block" type="submit">I forgot</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <a href="http://www.kukua.cc" title="Go to the Kukua homepage" target="_blank">www.kukua.cc</a>
                <a href="/auth/login" class="pull-right">no, I'm just kidding</a>
            </div>
        </div>
    </div>
{/block}
