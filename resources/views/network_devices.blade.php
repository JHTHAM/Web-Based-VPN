@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Devices in {{ $network->name }}</h2>

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

    <div class="d-flex gap-4">
        {{-- Router Form --}}
        <form action="{{ route('network.devices.store', $network->id) }}" method="POST" class="flex-fill">
            @csrf
            <label>Select Routers:</label>
            <select name="router_ids[]" multiple class="form-select" required>
                @foreach ($allRouters as $router)
                    <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip_address }})</option>
                @endforeach       
            </select>

            <button class="btn btn-success mt-2">Add Device</button>
        </form>

        {{-- Standalone Client Form --}}
        <form action="{{ route('network.standalone.store', $network->id) }}" method="POST" class="flex-fill">
            @csrf
            <label>Select Standalone Clients:</label>
            <select name="client_ids[]" multiple class="form-select" required>
                @foreach ($allStandaloneClients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->ip_address }})</option>
                @endforeach
            </select>

            <button class="btn btn-success mt-2">Add Device</button>
        </form>
    </div>

    {{-- Router Table --}}
    <table class="table mt-5">
        <colgroup>
            <col style="width: 30%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
        </colgroup>
        <thead>
            <tr>
                <th>Router Name</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($network->routers as $router)
                <tr>
                    <td>{{ $router->name }}</td>
                    <td>{{ $router->ip_address }}</td>
                    <td id="status-router-{{ $router->id }}">
                        @if($router->status === 'online')
                            <span class="badge bg-success">🟢 Online</span>
                        @else
                            <span class="badge bg-danger">🔴 Offline</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('network.devices.destroy', [$network->id, $router->id]) }}" 
                            method="POST" 
                            onsubmit="return confirm('Remove this device from the network?')">
                            @csrf 
                            @method('DELETE')
                            <button class="btn btn-warning">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Standalone Client Table --}}
    <table class="table mt-4">
        <colgroup>
            <col style="width: 30%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
        </colgroup>
        <thead>
            <tr>
                <th>Standalone Client Name</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($network->standaloneClients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->ip_address }}</td>
                    <td id="status-client-{{ $client->id }}">
                        @if($client->status === 'online')
                            <span class="badge bg-success">🟢 Online</span>
                        @else
                            <span class="badge bg-danger">🔴 Offline</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('network.standalone.destroy', [$network->id, $client->id]) }}" 
                            method="POST" 
                            onsubmit="return confirm('Remove this device from the network?')">
                            @csrf 
                            @method('DELETE')
                            <button class="btn btn-warning">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function updateStatus() {
        fetch('{{ url('/fetch-status') }}')
            .then(response => response.json())
            .then(data => {
                for (const [id, status] of Object.entries(data)) {
                    const statusEl = document.getElementById(`status-${id}`);
                    if (statusEl) {
                        statusEl.innerHTML = status === 'online'
                            ? '<span class="badge bg-success">🟢 Online</span>'
                            : '<span class="badge bg-danger">🔴 Offline</span>';
                    }
                }
            });
    }

    setInterval(updateStatus, 5000);
    updateStatus();

    // Flash message auto-hide
    setTimeout(() => {
        document.querySelectorAll('.fade-alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);

</script>
@endsection
