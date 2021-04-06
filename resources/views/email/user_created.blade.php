<p>Hi {{ $user->name }}!</p>
<p>Your email was used to create new user in {{ config('app.name') }}.</p>
<p>Please use these credentials for login:</p>
<p>
    Email: {{ $user->email }}
    <br>
    Password: {{ $password }}
</p>
<br>
<p>The {{ config('app.name') }} Team.</p>