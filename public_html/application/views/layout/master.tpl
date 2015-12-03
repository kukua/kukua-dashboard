<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kukua Dashboard</title>
    <link rel="stylesheet" href="/assets/dist/main.min.css" type="text/css">
</head>
<body>
    {literal}
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-64064681-2', 'auto');
            ga('send', 'pageview');
        </script>
    {/literal}

    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/" title="Go to the home screen">
                    Kukua {GlobalHelper::getCountry()}
                </a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    {if GlobalHelper::getUser() !== false}
                        <li><a href="/">Home</a></li>
                        <li><a href="/graph">Weather per location</a></li>
                        <li><a href="/forecast">Forecast</a></li>
                    {/if}
                </ul>
                {if GlobalHelper::getUser() !== false}
                    <ul class="nav navbar-nav pull-right">
                        <li><a href="/auth/logout">Logout</a></li>
                    </ul>
                {/if}
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    {block name=content}{/block}

    <script src="/assets/dist/main.min.js" type="text/javascript"></script>
</body>
</html>
