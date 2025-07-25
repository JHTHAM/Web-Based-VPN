@extends('layouts.app')

@section('content')
<div class="container">
    <h2>📥 Download VPN Profiles</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Router Name</th>
                <th>Label</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Download OVPN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($routers as $router)
                <tr>
                    <td>{{ $router->name }}</td>
                    <td>{{ $router->label }}</td>
                    <td>{{ $router->ip_address }}</td>
                    <td>
                        <span id="status-{{ $router->id }}" 
                            class="badge {{ $router->status === 'online' ? 'bg-success' : 'bg-danger' }}">
                            {{ $router->status === 'online' ? '🟢 Online' : '🔴 Offline' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $fileName = basename($router->ovpn_path);
                        @endphp

                        @if($router->ovpn_path && Storage::disk('local')->exists("ovpn/{$fileName}"))
                            <a href="{{ route('download.ovpn', $router->id) }}" class="btn btn-sm btn-success">
                                Download
                            </a>
                        @else
                            <span class="text-muted">Not generated</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function refreshStatuses() {
    fetch("{{ route('routers.status') }}")
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

    // Refresh every 5 seconds
    setInterval(refreshStatuses, 5000);
</script>
@endsection
