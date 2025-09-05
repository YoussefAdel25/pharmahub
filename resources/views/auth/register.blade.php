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
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror

                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">-- Select Role --</option>
                        <option value="supplier" {{ old('role') === 'supplier' ? 'selected' : '' }}>Supplier</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    @error('role')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3" id="region-wrapper"
                    style="display:{{ old('role') === 'customer' ? 'block' : 'none' }};">
                    <label>Region</label>
                    <select name="region_id" class="form-control">
                        <option value="">-- Select Region --</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button class="btn btn-success w-100">Register</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        window.addEventListener("load", function() {
            const roleSelect = document.getElementById("role");
            const regionWrapper = document.getElementById("region-wrapper");
            const regionSelect = regionWrapper.querySelector("select");




            roleSelect.addEventListener("change", function() {
                console.log("Role changed to:", this.value);

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
