@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Standalone Client Management</h2>

    @if (session('success'))
        <div class="alert alert-success fade-alert">{{ session('success') }}</div>
    @endif

    {{-- Create Client Form --}}
    <form action="{{ route('standalone_clients.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Enter standalone client name" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Create Client</button>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger fade-alert" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </form>

    {{-- Search Box --}}
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search standalone clients...">
    </div>

    {{-- Standalone Clients Table --}}
    <table class="table table-bordered table-striped" id="clientTable">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>IP Address</th>
                <th>Label</th>
                <th>Network</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($clients as $client)
        <tr>
            <form action="{{ route('standalone_clients.update', $client->id) }}" method="POST">
                @csrf @method('PUT')
                <td><input type="text" name="name" value="{{ $client->name }}" class="form-control" readonly></td>
                <td>{{ $client->ip_address }}</td>
                <td><input type="text" name="label" value="{{ $client->label }}" class="form-control"></td>
                <td>
                    @if ($client->networks->count())
                        {{ $client->networks->pluck('name')->join(', ') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <span id="status-{{ $client->id }}" 
                          class="badge {{ $client->status === 'online' ? 'bg-success' : 'bg-danger' }}">
                          {{ $client->status === 'online' ? '🟢 Online' : '🔴 Offline' }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-info">Update</button>
            </form>

            {{-- Delete Form--}}
            <form action="{{ route('standalone_clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Delete this client?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
function refreshStatuses() {
    fetch("{{ route('standalone_clients.status') }}")
        .then(response => response.json())
        .then(data => {
            for (const [id, status] of Object.entries(data)) {
                const badge = document.getElementById(`status-${id}`);
                if (badge) {
                    if (status === 'online') {
                        badge.classList.remove('bg-danger');
                        badge.classList.add('bg-success');
                        badge.textContent = '🟢 Online';
                    } else {
                        badge.classList.remove('bg-success');
                        badge.classList.add('bg-danger');
                        badge.textContent = '🔴 Offline';
                    }
                }
            }
        })
        .catch(error => console.error('Status update failed:', error));
    }
    setInterval(refreshStatuses, 5000);

    document.getElementById("searchInput").addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#clientTable tbody tr").forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    setTimeout(() => {
        document.querySelectorAll('.fade-alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);

</script>
@endsection
