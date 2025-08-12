@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Devices in {{ $network->name }}</h2>

    <form action="{{ route('network.devices.store', $network->id) }}" method="POST" class="mb-3">
        @csrf
        <label>Select Routers:</label>
        <select name="router_ids[]" multiple class="form-select" required>
            @foreach ($allRouters as $router)
                <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip_address }})</option>
            @endforeach
        </select>
        <button class="btn btn-success mt-2">Add Devices</button>
    </form>

    <table class="table">
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
                    <td id="status-{{ $router->id }}">
                        @if($router->status === 'online')
                            <span class="badge bg-success">🟢 Online</span>
                        @else
                            <span class="badge bg-danger">🔴 Offline</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('network.devices.destroy', [$network->id, $router->id]) }}" method="POST">
                            @csrf @method('DELETE')
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
</script>
@endsection
