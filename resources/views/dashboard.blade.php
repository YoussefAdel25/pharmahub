@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="card-title">Welcome, {{ auth()->user()->name }}</h1>
            <p class="card-text">
                You are logged in as <strong>{{ auth()->user()->role }}</strong>.
            </p>
        </div>
    </div>
@endsection
