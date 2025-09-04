@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="width: 500px; background: #ffffff;">

        <div class="text-center mb-4">
            <i class="bi bi-person-circle text-dark" style="font-size: 3rem;"></i>
            <h3 class="mt-2 text-dark">Create New User</h3>
            <p class="text-muted mb-0">Fill in the details below to create a new user account</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success fade-alert" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger fade-alert mt-2" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Create User Form --}}
        <form action="{{ route('admin_accounts.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="mb-3">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
            </div>

            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label for="company_name">Company Name</label>
                <input type="text" name="company_name" class="form-control" required value="{{ old('company_name') }}">
            </div>

            <div class="mb-3">
                <label for="company_address">Company Address</label>
                <input type="text" name="company_address" class="form-control" required value="{{ old('company_address') }}">
            </div>

            <div class="mb-3">
                <label for="role">Role</label>
                <select name="role" class="form-select" required>
                    @if(auth()->user()->role === 'superadmin')
                        <option value="admin">Admin</option>
                        <option value="client" selected>Client</option>
                    @else
                        {{-- Admins can only create clients --}}
                        <option value="client" selected>Client</option>
                    @endif
                </select>
            </div>
            
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin_accounts.index') }}" class="btn btn-outline-secondary w-50 fw-bold rounded-3">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary w-50 fw-bold rounded-3 shadow-sm">
                    <i class="bi bi-check2-circle"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    setTimeout(() => {
        document.querySelectorAll('.fade-alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
</script>
@endsection
