@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1>Edit User</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Leave blank to keep current password">
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="supplier" {{ $user->role == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
