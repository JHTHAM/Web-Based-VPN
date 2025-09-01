@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Overview VPN Profiles</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Routers --}}
    <table class="table table-bordered table-striped align-middle mt-3">
        <colgroup>
            <col style="width: 20%;">
            <col style="width: 20%;">
            <col style="width: 20%;">
            <col style="width: 15%;">
            <col style="width: 20%;">
        </colgroup>
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
                        <span id="status-router-{{ $router->id }}" 
                            class="badge {{ $router->status === 'online' ? 'bg-success' : 'bg-danger' }}">
                            {{ $router->status === 'online' ? '🟢 Online' : '🔴 Offline' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $fileName = basename($router->ovpn_path);
                        @endphp

                        @if($router->ovpn_path && Storage::disk('local')->exists("ovpn/{$fileName}"))
                            <a href="{{ route('router_download.ovpn', $router->id) }}" class="btn btn-sm btn-success">
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

    {{-- Standalone Clients --}}
    <table class="table table-bordered table-striped align-middle mt-5">
        <colgroup>
            <col style="width: 20%;">
            <col style="width: 20%;">
            <col style="width: 20%;">
            <col style="width: 15%;">
            <col style="width: 20%;">
        </colgroup>
        <thead class="table-dark">
            <tr>
                <th>Standalone Client Name</th>
                <th>Label</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Download OVPN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($standaloneClients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->label }}</td>
                    <td>{{ $client->ip_address }}</td>
                    <td>
                        <span id="status-client-{{ $client->id }}" 
                            class="badge {{ $client->status === 'online' ? 'bg-success' : 'bg-danger' }}">
                            {{ $client->status === 'online' ? '🟢 Online' : '🔴 Offline' }}
                        </span>
                    </td>
                    <td>
                        @php
                            $fileName = basename($client->ovpn_path);
                        @endphp

                        @if($client->ovpn_path && Storage::disk('local')->exists("ovpn/{$fileName}"))
                            <a href="{{ route('client_download.ovpn', $client->id) }}" class="btn btn-sm btn-success">
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
