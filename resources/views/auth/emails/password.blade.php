<p>Dear User,</p>

<p>Please use the link below to reset your passsord.</p>

<p><a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a></p>


