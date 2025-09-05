@extends('layouts.app')

@section('content')
    <div class="container py-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
            <h2 class="mb-4 fw-bold text-primary text-center">Orders for Your Products</h2>

            @if ($orders->isEmpty())
                <div class="alert alert-info shadow-sm rounded text-center">No orders found for your products yet.</div>
            @else
                <div class="row g-4">
                    @foreach ($orders as $order)
                        @php
                            $statusColors = [
                                'pending' => ['#ffc107', '#ffecb3', '#212529'],
                                'partially_completed' => ['#0dcaf0', '#cff4fc', '#212529'],
                                'in_delivery' => ['#0d6efd', '#cfe2ff', '#fff'],
                                'delivered' => ['#198754', '#d4edda', '#fff'],
                                'cancelled' => ['#dc3545', '#f8d7da', '#fff'],
                            ];
                            [$bgStart, $bgEnd, $textColor] = $statusColors[$order->status] ?? ['#eee', '#ccc', '#000'];

                            $supplierItems = $order->items->filter(
                                fn($item) => $item->product->supplier_id == auth()->id(),
                            );
                            $missingItems = $supplierItems->filter(
                                fn($item) => $item->quantity_executed < $item->quantity_requested,
                            );
                        @endphp

                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm border-0 hover-card h-100">
                                <!-- Header -->
                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="background: linear-gradient(90deg, {{ $bgStart }}, {{ $bgEnd }}); color: {{ $textColor }}; border-radius: .5rem .5rem 0 0;">
                                    <div>
                                        <strong>Order #{{ $order->id }}</strong><br>
                                        <small>{{ $order->user->name }} | {{ $order->user->region->name ?? 'N/A' }}</small>
                                    </div>
                                    <span
                                        class="badge status-badge">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                </div>

                                <!-- Table -->
                                <div class="card-body p-0">
                                    <table class="table table-hover table-sm mb-0 align-middle text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Req.</th>
                                                <th>Exec.</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($supplierItems as $item)
                                                <tr
                                                    class="{{ $item->quantity_executed < $item->quantity_requested ? 'table-warning' : '' }}">
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->quantity_requested }}</td>
                                                    <td>{{ $item->quantity_executed }}</td>
                                                    <td>${{ number_format($item->price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Footer -->
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <strong>Total: </strong>
                                    <span>${{ number_format($supplierItems->sum(fn($i) => $i->quantity_executed * $i->price), 2) }}</span>

                                    <div class="d-flex flex-wrap gap-2">
                                        <!-- Add Missing -->
                                        @if ($order->status === 'partially_completed' && $missingItems->count())
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-pill shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#addMissingModal{{ $order->id }}">
                                                <i class="bi bi-plus-circle me-1"></i> Add Missing
                                            </button>
                                        @endif

                                        <!-- Change Status -->
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#changeStatusModal{{ $order->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Change Status
                                        </button>
                                        <!-- Cancel -->
                                        @if ($order->status !== 'cancelled')
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill shadow-sm "
                                                data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->id }}">
                                                <i class="bi bi-x-circle me-1"></i> Cancel Order
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Add Missing Quantities -->
                        @if ($missingItems->count())
                            <div class="modal fade" id="addMissingModal{{ $order->id }}" tabindex="-1"
                                aria-labelledby="addMissingModalLabel{{ $order->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('supplier.orders.updateStatus', $order->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addMissingModalLabel{{ $order->id }}">Add
                                                    Missing Quantities - Order #{{ $order->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                @foreach ($missingItems as $item)
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ $item->product->name }} (Missing:
                                                            {{ $item->quantity_requested - $item->quantity_executed }})</label>
                                                        <input type="number" name="quantities[{{ $item->id }}]"
                                                            class="form-control" min="1"
                                                            max="{{ $item->quantity_requested - $item->quantity_executed }}"
                                                            value="{{ $item->quantity_requested - $item->quantity_executed }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Quantities</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Modal Change Status -->
                        <div class="modal fade" id="changeStatusModal{{ $order->id }}" tabindex="-1"
                            aria-labelledby="changeStatusModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('supplier.orders.changeStatus', $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changeStatusModalLabel{{ $order->id }}">Change
                                                Status - Order #{{ $order->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <select name="status" class="form-select" required>
                                                @php
                                                    $statuses = [
                                                        'pending' => 'Pending',
                                                        'partially_completed' => 'Partially Completed',
                                                        'in_delivery' => 'In Delivery',
                                                        'delivered' => 'Delivered',
                                                        'cancelled' => 'Cancelled',
                                                    ];
                                                @endphp
                                                @foreach ($statuses as $key => $label)
                                                    <option value="{{ $key }}"
                                                        {{ $order->status === $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1"
                            aria-labelledby="cancelModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cancelModalLabel{{ $order->id }}">
                                            Confirm Cancel Order</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to cancel Order #{{ $order->id }}? This action
                                        cannot be undone.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <form action="{{ route('supplier.orders.cancel', $order->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Yes, Cancel
                                                Order</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
    </div>

    <style>
        .hover-card {
            transition: transform .25s, box-shadow .25s;
            border-radius: .5rem;
        }

        .hover-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .status-badge {
            padding: .35em .65em;
            border-radius: .5rem;
            font-size: .85rem;
            font-weight: 500;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .btn {
            transition: transform .15s, box-shadow .15s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection
