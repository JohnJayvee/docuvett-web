<div>
    <p>Hi</p>
    @if ($title)
        <div>Title: {{$title}}</div><br>
    @endif
    <br>
    @if ($error)
        <div>Error:</div><br>
        <div>{{ $error }}</div>
    @endif
    <br>
</div>