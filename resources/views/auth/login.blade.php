<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label>Email</label>
            <input type="email" name="email" required {{ $loginAttempt->attempts >= 9 ? 'disabled' : '' }}>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required {{ $loginAttempt->attempts >= 9 ? 'disabled' : '' }}>
        </div>
        <div>
            <button type="submit" {{ $loginAttempt->attempts >= 9 ? 'disabled' : '' }}>Login</button>
    </div>
</form>
</body>
</html>