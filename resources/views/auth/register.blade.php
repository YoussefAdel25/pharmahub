@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">Register</h2>
        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            {{-- Select Region --}}
            <div class="mb-3">
                <label>Region</label>
                <select name="region_id" class="form-control" required>
                    <option value="">-- Select Region --</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <option value="supplier">Supplier</option>
                    <option value="pharmacy">Pharmacy</option>
                </select>
            </div>

            <button class="btn btn-success w-100">Register</button>
        </form>
    </div>
</div>
@endsection
