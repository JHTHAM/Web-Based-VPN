@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="width: 500px; background: #ffffff;">

        <!-- Header -->
        <div class="text-center mb-4">
            <i class="bi bi-person-circle text-dark" style="font-size: 3rem;"></i>
            <h3 class="mt-2 text-dark">Edit Profile</h3>
            <p class="text-muted mb-0">Update your account details below</p>
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

        <!-- Profile Update Form -->
        <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label text-dark">Username</label>
                <input type="text" name="username"
                       value="{{ old('username', Auth::user()->username) }}"
                       required class="form-control rounded-3">
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Email</label>
                <input type="email" name="email"
                       value="{{ old('email', Auth::user()->email) }}"
                       required class="form-control rounded-3">
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Company Name</label>
                <input type="text" name="company_name"
                       value="{{ old('company_name', Auth::user()->company_name ?? '') }}"
                       class="form-control rounded-3">
            </div>

            <div class="mb-4">
                <label class="form-label text-dark">Company Address</label>
                <input type="text" name="company_address"
                       value="{{ old('company_address', Auth::user()->company_address ?? '') }}"
                       class="form-control rounded-3">
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary w-50 fw-bold rounded-3">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary w-50 fw-bold rounded-3 shadow-sm">
                    <i class="bi bi-check2-circle"></i> Save
                </button>
            </div>
        </form>

        <hr class="my-3">

        <!-- Edit Password Button -->
        <div class="text-center">
            <a href="{{ route('profile.password.edit') }}" class="btn btn-warning fw-bold rounded-3 shadow-sm">
                <i class="bi bi-shield-lock-fill"></i> Change Password
            </a>
        </div>
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
