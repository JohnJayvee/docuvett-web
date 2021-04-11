<p>Hi {{ $user->name }}!</p>
<p>Registration process finished successfully.</p>
<p>Please use these credentials for login:</p>
<p>
    Email: {{ $user->email }}
    <br>
    Password: {{ $password }}
</p>
<br>
<p>The {{ config('app.name') }} Team.</p>