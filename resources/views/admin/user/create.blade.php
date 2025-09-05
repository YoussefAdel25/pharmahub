@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Create User</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="supplier">Supplier</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
        <button class="btn btn-success">Create</button>
    </form>
</div>
@endsection

