<html>
<body>
    <h1>Dear {$user->first_name} {$user->last_name},</h1>
    <p>Thank you for your interest in the Kukua Dashboard. By clicking on the following link you will be able to start using your free one month trial:</p>
    <p><a href="{$base}auth/activate/{$user->activation_code}">{$base}auth/activate/{$user->activation_code}</a></p>
    <p>In this trial you will have unlimited access to the historical data and forecasts from our Tanzania weather stations and we are extremely excited to hear what you think.</p>
    <p>As we are still testing we are looking to get as much feedback as possible, and this means you will be rewarded with more free months when you give us consistent feedback on our iterations. Please write your feedback in the feedback box on the trial page or send me an email on ollie@kukua.cc</p>
    <p>Kind regards,</p>
    <p>
        Ollie Smeenk<br>
        Chief Product Officer.<br>
        Kukua B.V.<br>
    </p>
</body>
</html>
