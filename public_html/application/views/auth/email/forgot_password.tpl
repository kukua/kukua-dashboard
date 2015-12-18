<html>
<body>
	<h1>Hi {$user->first_name} {$user->last_name}!</h1>
	<p>Please follow this link to reset your password</p>
    <a href="{$base}auth/reset_password/{$code}">Reset password</a>
</body>
</html>
