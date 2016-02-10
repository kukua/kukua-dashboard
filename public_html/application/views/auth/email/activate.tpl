<html>
<head>
    <style>
        p {
            margin-bottom: 5px;
            margin-top: 1em;
        }
    </style>
</head>
<body>
    <p>Dear {$user->first_name} {$user->last_name},</p>
    <p>Welcome to the Kukua Dashboard. Please verify your details using the following link:</p>
    <p><a href="{$base}auth/activate/{$user->activation_code}">{$base}auth/activate/{$user->activation_code}</a></p>
    <p>Once you have verified your name and password, you will be able to access our weather graphs and forecast map. Feel free to email us or contact us via Social Media if you have any requests.</p>
    <p>Kind regards,</p>
    <p>
        The Kukua Team<br>
    </p>
    <p>
        <img src="{$base}/assets/img/kukua-logo-small.png"><br>
        Kukua B.V.<br>
        info@kukua.cc<br>
        <a href="https://facebook.com/kukuaweather">Facebook</a><br>
        <a href="https://twitter.com/kukuaweather">Twitter</a><br>
        <a href="https://github.com/kukua">GitHub</a>
    </p>
</body>
</html>
