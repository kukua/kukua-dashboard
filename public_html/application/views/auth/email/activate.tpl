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
    <p>Thank you for your interest in the Kukua Dashboard. By clicking on the following link you will be able to start using your free one month trial:</p>
    <p><a href="{$base}auth/activate/{$user->activation_code}">{$base}auth/activate/{$user->activation_code}</a></p>
    <p>In this trial you will have unlimited access to the historical data and forecasts from our Tanzania weather stations and we are extremely excited to hear what you think.</p>
    <p>As we are still testing we are looking to get as much feedback as possible, and this means you will be rewarded with more free months when you give us consistent feedback on our iterations. Please write your feedback in the feedback box on the trial page or send me an email on <a href="mailto:ollie@kukua.cc">ollie@kukua.cc</a></p>
    <p>We are looking to test this product for free with as many industry professionals as possible. Please add the email addresses of contacts you believe can benefit from- and give good feedback on our free demo so that we can send them an invitation too. As this is a demo we have not yet included some of the weather parameters such as wind and humidity, and we hope to receive your feedback on the usability and features of the dashboard.</p>
    <p>Kind regards,</p>
    <p>
        Ollie Smeenk<br>
        Chief Product Officer.<br>
        Kukua B.V.<br>
    </p>
    <p><img src="{$base}/assets/img/kukua-logo-small.png"></p>
</body>
</html>
