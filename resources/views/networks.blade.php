@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Networks</h2>

    <form action="{{ route('networks.store') }}" method="POST" class="mb-3">
        @csrf
        <input type="text" name="name" placeholder="New Network Name" required>
        <button class="btn btn-primary">Add Network</button>

        @if ($errors->any())
            <div class="alert alert-danger fade-alert" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success fade-alert" role="alert">
                {{ session('success') }}
            </div>
        @endif
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Network Name</th>
                <th>Devices</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($networks as $network)
            <tr>
                <td>
                    <a href="{{ route('network.devices', $network->id) }}">
                        {{ $network->name }}
                    </a>
                </td>
                <td>
                    {{ $network->routers_count + $network->standalone_clients_count }}
                </td>
                <td>
                    {{-- Delete Form --}}
                    <form action="{{ route('networks.destroy', $network->id) }}" method="POST"
                        onsubmit="return confirm('Delete this network?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
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
