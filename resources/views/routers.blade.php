@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Router Management</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Create Router Form --}}
    <form action="{{ route('routers.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Enter router name" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Create Router</button>
            </div>
        </div>
    </form>

    {{-- Search Box --}}
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search routers...">
    </div>

    {{-- Router Table --}}
    <table class="table table-bordered table-striped" id="routerTable">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>IP Address</th>
                <th>Label</th>
                <th>Networks</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($routers as $router)
    <tr>
        {{-- Update Form --}}
        <form action="{{ route('routers.update', $router->id) }}" method="POST">
            @csrf
            @method('PUT')

            <td>
                <input type="text" name="name" value="{{ $router->name }}" class="form-control" readonly>
            </td>
            <td>{{ $router->ip_address }}</td>
            <td>
                <input type="text" name="label" value="{{ $router->label }}" class="form-control">
            </td>
            <td>1</td>
            <td>
                @if($router->status === 'online')
                    <span class="badge bg-success">🟢 Online</span>
                @else
                    <span class="badge bg-danger">🔴 Offline</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-info">Update</button>
        </form>

        {{-- Delete Form --}}
        <form action="{{ route('routers.destroy', $router->id) }}" method="POST" onsubmit="return confirm('Delete this router?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </tr>
@endforeach

        </tbody>
    </table>
</div>

{{-- Search JS --}}
<script>
    document.getElementById("searchInput").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("#routerTable tbody tr");

        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? "" : "none";
        });
    });
</script>
@endsection
