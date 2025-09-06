@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg border-0 rounded-3" style="max-width: 480px; width: 100%;">
        <div class="card-body p-5">
            <h2 class="mb-4 text-center text-primary fw-bold">Register</h2>
            <form action="{{ route('register.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input type="text" id="name" name="name"
                           class="form-control form-control-lg rounded-2 @error('name') is-invalid @enderror"
                           placeholder="Enter your name" value="{{ old('name') }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" id="email" name="email"
                           class="form-control form-control-lg rounded-2 @error('email') is-invalid @enderror"
                           placeholder="Enter your email" value="{{ old('email') }}" required>
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

                <!-- Role -->
                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">Role</label>
                    <select id="role" name="role"
                            class="form-select form-select-lg rounded-2 @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        <option value="supplier" {{ old('role') === 'supplier' ? 'selected' : '' }}>Supplier</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    @error('role')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Region (shown only for customers) -->
                <div class="mb-3" id="region-wrapper" style="display:{{ old('role') === 'customer' ? 'block' : 'none' }};">
                    <label for="region_id" class="form-label fw-semibold">Region</label>
                    <select id="region_id" name="region_id"
                            class="form-select form-select-lg rounded-2 @error('region_id') is-invalid @enderror">
                        <option value="">-- Select Region --</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button class="btn btn-primary btn-lg w-100 shadow-sm mt-3">Register</button>
            </form>

            <!-- Divider -->
            <div class="text-center my-4">
                <span class="text-muted">or</span>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="mb-0">Already have an account?
                    <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    window.addEventListener("load", function() {
        const roleSelect = document.getElementById("role");
        const regionWrapper = document.getElementById("region-wrapper");
        const regionSelect = document.getElementById("region_id");

        roleSelect.addEventListener("change", function() {
            if (this.value === "customer") {
                regionWrapper.style.display = "block";
                regionSelect.setAttribute("required", "required");
            } else {
                regionWrapper.style.display = "none";
                regionSelect.removeAttribute("required");
                regionSelect.value = "";
            }
        });
    });
</script>
@endsection
