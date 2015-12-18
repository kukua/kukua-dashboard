<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kukua Dashboard</title>
    <link rel="stylesheet" href="/assets/dist/main.min.css" type="text/css">
</head>
<body>
    {if (ENVIRONMENT == "production")}
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
    {/if}

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
                    Kukua {$user->country}
                </a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                {if $user !== false}
                    <ul class="nav navbar-nav">
                        <li><a href="/">Home</a></li>
                        <li><a href="/graph">Weather graph</a></li>
                        <li><a href="/forecast">Forecast map</a></li>
                    </ul>
                    <ul class="nav navbar-nav pull-right">

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Hi {$user->first_name}! <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/user/update/{$user->id}">My account</a></li>
                                {if $isAdmin}
                                    <li><a href="/user">Users</a></li>
                                {/if}
                                <li role="separator" class="divider"></li>
                                <li><a href="/auth/logout">Logout</a></li>
                            </ul>
                        </li>
                        <li><a href="#"></a></li>
                    </ul>
                {/if}
            </div>
        </div>
    </nav>

    {block name=content}{/block}

    <script src="/assets/dist/main.min.js" type="text/javascript"></script>
</body>
</html>
