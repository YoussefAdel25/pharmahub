@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg border-0 rounded-3" style="max-width: 420px; width: 100%;">
        <div class="card-body p-5">

            <form action="{{ route('login') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <input type="email" id="email" name="email"
                           class="form-control form-control-lg rounded-2 @error('email') is-invalid @enderror"
                           placeholder="Enter your email" required autofocus>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" id="password" name="password"
                           class="form-control form-control-lg rounded-2 @error('password') is-invalid @enderror"
                           placeholder="Enter your password" required>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>



                <button class="btn btn-primary btn-lg w-100 shadow-sm">Login</button>
            </form>

            <!-- Divider -->
            <div class="text-center my-4">
                <span class="text-muted">or</span>
            </div>

            <!-- Signup -->
            <div class="text-center">
                <p class="mb-0">Don't have an account?
                    <a href="{{ route('register') }}" class="text-decoration-none text-primary fw-semibold">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
