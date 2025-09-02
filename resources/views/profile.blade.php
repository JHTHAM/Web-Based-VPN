@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">

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

            <!-- Profile Card -->
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">My Profile</h3>
                </div>

                <div class="card-body p-4">
                    <!-- Profile Info -->
                    <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-person-circle fs-3 me-3 text-primary"></i>
                        <div>
                            <strong>Username:</strong>
                            <p class="mb-0">{{ Auth::user()->username }}</p>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-envelope-fill fs-3 me-3 text-primary"></i>
                        <div>
                            <strong>Email:</strong>
                            <p class="mb-0">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-building fs-3 me-3 text-primary"></i>
                        <div>
                            <strong>Company Name:</strong>
                            <p class="mb-0">{{ Auth::user()->company_name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill fs-3 me-3 text-primary"></i>
                        <div>
                            <strong>Company Address:</strong>
                            <p class="mb-0">{{ Auth::user()->company_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary px-4">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </a>
                </div>
            </div>

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
