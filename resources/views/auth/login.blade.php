<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Nova+ VPN | Login')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

@if (session('success'))
    <div class="alert alert-success fade-alert" role="alert">
        {{ session('success') }}
    </div>
@endif

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div style="text-align: center">
                        <img src="{{ asset('Image/icon.jpg') }}" alt="Logo" 
                            style="width: 400px; height: 150px; border-radius: 1%; margin-bottom: 5px">
            </div>
            
            <div class="card shadow">
                
                <div class="card-header text-center">
                
                    <h4>Scalable. Secure. Next-Gen VPN Management.</h4>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="username"
                                   class="form-control"
                                   id="username"
                                   name="username"
                                   placeholder="Enter username"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   name="password"
                                   placeholder="Enter password"
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
// Flash message auto-hide
    setTimeout(() => {
        document.querySelectorAll('.fade-alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
</script>

</body>
</html>
