<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Nova+ WebAccess_VPN')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('Image/favicon.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
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
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ asset('Image/icon.jpg') }}" alt="Logo" 
                style="width: 90px; height: 90px; border-radius: 50%; margin-bottom: 10px;">

            <h2>WebBasedVpn</h2>
        </div>
        <a href="{{ url('/dashboard') }}"><i class="mdi mdi-chart-line"></i> Dashboard</a>
        <a href="{{ url('/routers') }}"><i class="mdi mdi-lan"></i> Routers</a>
        <a href="{{ url('/networks') }}"><i class="mdi mdi-access-point-network"></i> Networks</a>
        <a href="#" class="nav-link" id="devicesSidebarToggle">
            <i class="mdi mdi-lan-connect"></i>
            <span>Devices in Networks ▾</span>
        </a>
        <ul id="devicesSidebarMenu" class="nav flex-column ms-3" style="display: none;">
            @foreach(\App\Models\Network::all() as $network)
                <li class="nav-item">
                    <a href="{{ route('network.devices', ['network' => $network->id]) }}" class="nav-link">
                        {{ $network->name }}
                    </a>
                </li>
            @endforeach
        </ul>
        <a href="{{ url('/standalone_vpn_clients') }}"><i class="mdi mdi-laptop"></i> Standalone VPN Clients</a>
        <a href="{{ url('/administration') }}"><i class="mdi mdi-account-multiple"></i> Administration</a>
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
                        <a href="{{ url('/profile') }}"><i class="mdi mdi-account"></i> Profile</a>
                        <form method="POST" action="{{ route('logout') }}"
                            onsubmit="return confirm('Are you sure you want to logout?');">
                            @csrf
                            <button type="submit"><i class="mdi mdi-home-import-outline"></i> Logout</button>
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

        // Devices sidebar dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const toggle = document.getElementById("devicesSidebarToggle");
            const menu = document.getElementById("devicesSidebarMenu");

            toggle.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent navigation
                menu.style.display = (menu.style.display === "none" ? "block" : "none");
            });
        });
    </script>
</body>
</html>
