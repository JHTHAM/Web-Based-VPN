@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Networks</h2>

    <form action="{{ route('networks.store') }}" method="POST" class="mb-3">
        @csrf
        <input type="text" name="name" placeholder="New Network Name" required>
        <button class="btn btn-primary">Add Network</button>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>       
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
                    <td><a href="{{ route('network.devices', $network->id) }}">{{ $network->name }}</a></td>
                    <td>{{ $network->routers_count }}</td>
                    <td>
                        <form action="{{ route('networks.destroy', $network) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
