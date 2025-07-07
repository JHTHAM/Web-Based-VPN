@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p>Welcome to your dashboard, {{ Auth::user()->name }}.</p>
@endsection
