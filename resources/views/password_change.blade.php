@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="width: 500px; background: #ffffff;">

        <!-- Header -->
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock-fill text-dark" style="font-size: 3rem;"></i>
            <h3 class="mt-2 text-dark">Change Password</h3>
            <p class="text-muted mb-0">Update your account security</p>
        </div>

        <!-- flash Alerts -->
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

        <!-- Password Update Form -->
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label text-dark">Current Password</label>
                <input type="password" name="current_password" required class="form-control rounded-3">
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">New Password</label>
                <input type="password" name="password" required class="form-control rounded-3">
            </div>

            <div class="mb-4">
                <label class="form-label text-dark">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="form-control rounded-3">
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-50 fw-bold rounded-3">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary w-50 fw-bold rounded-3 shadow-sm">
                    <i class="bi bi-check2-circle"></i> Update
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
