@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="bi bi-person-circle me-2"></i> Personal Settings
                    </div>

                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH') 
                            <div class="mb-3">
                                <label class="form-label fw-bold">Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            @if ($user->role === 'customer')
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Region</label>
                                    <select name="region_id" class="form-select @error('region_id') is-invalid @enderror">
                                        <option value="">-- Select Region --</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ old('region_id', $user->region_id) == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif

                            <hr class="my-4">

                            <h5 class="fw-bold text-secondary">Change Password</h5>

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Password</label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">New Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bi bi-check2-circle me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
