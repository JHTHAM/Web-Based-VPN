<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | VPN Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Register Account</h4>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="username">Full Name</label>
                            <input type="text"
                                   class="form-control"
                                   name="username"
                                   value="{{ old('username') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password">Password</label>
                            <input type="password"
                                   class="form-control"
                                   name="password"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password"
                                   class="form-control"
                                   name="password_confirmation"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="role">Register as</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="admin">Admin</option>
                                <option value="client">Client</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Register</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <small>Already have an account? <a href="{{ route('login') }}">Login</a></small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
