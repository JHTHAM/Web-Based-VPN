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
                        @if($router->status === 'online')
                            <span class="badge bg-success">🟢 Online</span>
                        @else
                            <span class="badge bg-danger">🔴 Offline</span>
                        @endif
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
@endsection
