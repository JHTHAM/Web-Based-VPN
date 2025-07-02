<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav style="background: #f2f2f2; padding: 1rem;">
        <span>Logged in as: {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>

        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" style="margin-left: 20px;">Logout</button>
        </form>
    </nav>

    <main style="padding: 2rem;">
        @yield('content')
    </main>
</body>
</html>
