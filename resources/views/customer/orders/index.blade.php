@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4 text-primary fw-bold">My Orders</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($orders->isEmpty())
            <div class="alert alert-info">You have no orders yet.</div>
        @else
            <div class="accordion" id="ordersAccordion">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'in_delivery' => 'info',
                            'delivered' => 'success',
                            'partially_completed' => 'info',
                            'cancelled' => 'danger',
                        ];
                        $badgeColor = $statusColors[$order->status] ?? 'secondary';
                    @endphp

                    <div class="accordion-item mb-3 shadow-sm rounded">
                        <h2 class="accordion-header" id="heading{{ $order->id }}">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $order->id }}">
                                <span class="me-2">Order #{{ $order->id }}</span>
                                <span
                                    class="badge bg-{{ $badgeColor }} text-white me-2">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                <span class="ms-auto fw-bold">Total: ${{ number_format($order->total_price, 2) }}</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $order->id }}" class="accordion-collapse collapse"
                            data-bs-parent="#ordersAccordion">
                            <div class="accordion-body">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Product</th>
                                            <th>Requested</th>
                                            <th>Executed</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->quantity_requested }}</td>
                                                <td>{{ $item->quantity_executed }}</td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @if ($order->status !== 'cancelled' && $order->status !== 'delivered')
                                    <!-- Cancel Button -->
                                    <button type="button" class="btn btn-danger mt-2" data-bs-toggle="modal"
                                        data-bs-target="#cancelModal{{ $order->id }}">
                                        <i class="bi bi-x-circle me-1"></i> Cancel Order
                                    </button>

                                    <!-- Cancel Confirmation Modal -->
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
                                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">Yes, Cancel
                                                            Order</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
