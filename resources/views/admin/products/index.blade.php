@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div id="alertContainer"></div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">All Products</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Quota</th>
                            <th>Supplier</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $product)
                            @php
                                $key = $product->supplier_id . '-' . $product->id;
                                $discountRate = isset($discounts[$key]) ? $discounts[$key]->discount_rate : 0;
                                $discountedPrice = $product->price * (1 - $discountRate / 100);
                            @endphp

                            <tr id="productRow{{ $product->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($product->image)
                                        <a href="{{ route('products.show', $product->id) }}">
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        </a>
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                </td>
                                <td>
                                    @if ($discountRate > 0)
                                        <span class="text-decoration-line-through">${{ $product->price }}</span><br>
                                        <span class="text-success fw-bold">${{ number_format($discountedPrice, 2) }}</span>
                                    @else
                                        <span class="fw-bold">${{ $product->price }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary">{{ ucfirst($product->type ?? 'N/A') }}</span></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $product->quota_period ? ucfirst(str_replace('_', ' ', $product->quota_period)) : 'N/A' }}
                                    </span>
                                </td>
                                <td><span class="fw-bold">{{ $product->supplier->name ?? 'N/A' }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No products available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteProductName"></strong>?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let deleteProductId = null;

        $(document).ready(function() {
            $('.delete-btn').click(function() {
                deleteProductId = $(this).data('id');
                $('#deleteProductName').text($(this).data('name'));
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });

            $('#confirmDeleteBtn').click(function() {
                if (!deleteProductId) return;

                $.ajax({
                    url: '/supplier/products/delete/' + deleteProductId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        $('#productRow' + deleteProductId).fadeOut();
                        let alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                        $('#alertContainer').html(alertHtml);
                        setTimeout(() => $('.alert').alert('close'), 3000);
                    },
                    error: function() {
                        alert('Something went wrong!');
                    }
                });
            });
        });
    </script>
@endsection
