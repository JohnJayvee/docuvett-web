<p>Hi {{ $user->name }}!</p>
<p>
    We got a request to reset your {{ config('app.name') }} password.
    <br>
    You can change your password using this link:
    <br>
    <a href='{{ $link }}'>{{ $link }}</a>
</p>
<p>If you ignore this message, your password won't change.</p>
<br>
<p>The {{ config('app.name') }} Team.</p>