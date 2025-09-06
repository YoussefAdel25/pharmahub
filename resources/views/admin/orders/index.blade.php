@extends('layouts.app')

@section('content')
    <div class="container-fluid py-5">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($orders->isEmpty())
            <div class="alert alert-info text-center shadow-sm rounded">No orders found in the system.</div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Region</th>
                                <th>Products</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Supplier(s)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'partially_completed' => 'info',
                                        'in_delivery' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->user->region->name ?? 'N/A' }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach ($order->items as $item)
                                                <li>
                                                    {{ $item->product->name }}
                                                    (x{{ $item->quantity_requested }})
                                                    <br>
                                                    <small class="text-muted">
                                                        Supplier: {{ $item->product->supplier->name ?? 'N/A' }}
                                                    </small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="fw-bold text-success">
                                        ${{ number_format($order->items->sum(fn($i) => $i->quantity_executed * $i->price), 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        @foreach ($order->items->pluck('product.supplier.name')->unique() as $supplierName)
                                            <span class="badge bg-light text-dark">{{ $supplierName }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($order->status !== 'cancelled')
                                            <!-- Cancel Button Trigger -->
                                            <button type="button"
                                                class="btn btn-sm btn-gradient-danger px-3 py-1 shadow-sm rounded-pill"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cancelOrderModal{{ $order->id }}">
                                                <i class="bi bi-x-octagon-fill me-1"></i> Cancel
                                            </button>

                                            <!-- Cancel Modal -->
                                            <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1"
                                                aria-labelledby="cancelOrderModalLabel{{ $order->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                                                        <div class="modal-header bg-gradient-danger text-white">
                                                            <h5 class="modal-title fw-bold"
                                                                id="cancelOrderModalLabel{{ $order->id }}">
                                                                <i class="bi bi-x-octagon me-2"></i> Cancel Order
                                                                #{{ $order->id }}
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="mb-3 fs-6">
                                                                Are you sure you want to <strong
                                                                    class="text-danger">cancel</strong>
                                                                this order? <br>
                                                                <span class="text-muted small">This action cannot be
                                                                    undone.</span>
                                                            </p>

                                                            <div class="border rounded p-3 bg-light">
                                                                <ul class="mb-0 small">
                                                                    <li><strong>Customer:</strong> {{ $order->user->name }}
                                                                    </li>
                                                                    <li><strong>Region:</strong>
                                                                        {{ $order->user->region->name ?? 'N/A' }}</li>
                                                                    <li><strong>Total:</strong>
                                                                        ${{ number_format($order->items->sum(fn($i) => $i->quantity_executed * $i->price), 2) }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer d-flex justify-content-between">
                                                            <button type="button" class="btn btn-light rounded-pill px-3"
                                                                data-bs-dismiss="modal">
                                                                <i class="bi bi-arrow-left me-1"></i> Back
                                                            </button>
                                                            <form
                                                                action="{{ route('supplier.orders.cancel', $order->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-gradient-danger rounded-pill px-3">
                                                                    <i class="bi bi-x-circle me-1"></i> Confirm Cancel
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@push('styles')
    <style>
        /* Attractive gradient button */
        .btn-gradient-danger {
            background: linear-gradient(135deg, #ff4d4d, #dc3545);
            color: #fff;
            border: none;
            transition: 0.3s;
        }

        .btn-gradient-danger:hover {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        /* Gradient header for modal */
        .bg-gradient-danger {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
        }
    </style>
@endpush
