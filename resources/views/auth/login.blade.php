<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | VPN Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Login to VPN Control Dashboard</h4>
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
                            <label for="email" class="form-label">Email address</label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   placeholder="Enter email"
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

                        <div class="mb-3">
                            <label for="role" class="form-label">Login as</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Select Role --</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                                <option value="client">Client</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>

                    </form>

                    <div class="mt-3 text-center">
                        <small>Don't have an account? <a href="{{ route('register') }}">Register</a></small>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
