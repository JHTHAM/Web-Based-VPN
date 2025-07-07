<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            padding: 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .sidebar a:hover {
            background: #34495e;
        }

        .main-content {
            flex: 1;
            background: #f5f5f5;
        }

        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px 20px;
            background: #fff;
            border-bottom: 1px solid #ccc;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            cursor: pointer;
            padding: 8px 12px;
            background: #3498db;
            color: #fff;
            border-radius: 4px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 6px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-width: 160px;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-menu a,
        .dropdown-menu form {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            background: #fff;
            width: 100%;
        }

        .dropdown-menu a:hover,
        .dropdown-menu form:hover {
            background: #f2f2f2;
        }

        .dropdown-menu form button {
            background: none;
            border: none;
            text-align: left;
            width: 100%;
            padding: 0;
        }

        .content-area {
            padding: 20px;
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <h2>MyApp</h2>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <a href="{{ url('/profile') }}">Profile</a>

        @if (Auth::check() && Auth::user()->username === 'superadmin')
            <a href="{{ url('/superadmin/manage') }}">Manage Users</a>
        @elseif (Auth::check() && Auth::user()->username === 'admin')
            <a href="{{ url('/admin/clients') }}">Client List</a>
        @endif
    </div>

    {{-- Main content area --}}
    <div class="main-content">
        <div class="topbar">
            @auth
                <div class="dropdown" id="userDropdown">
                    <div class="dropdown-toggle" onclick="toggleDropdown()"> 
                        {{ Auth::user()->name }} ({{ Auth::user()->username }})
                    </div>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="{{ url('/profile') }}">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('userDropdown');
        const menu = document.getElementById('dropdownMenu');

        function toggleDropdown() {
            menu.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!toggleBtn.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    </script>

</body>
</html>
