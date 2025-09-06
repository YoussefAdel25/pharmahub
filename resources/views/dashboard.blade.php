@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 bg-light min-vh-100">
    @if(auth()->user()->role === 'admin')
    <h2 class="fw-bold mb-4 text-dark">📊 Admin Dashboard</h2>
    @elseif(auth()->user()->role === 'supplier')
    <h2 class="fw-bold mb-4 text-dark">📊 Supplier Dashboard</h2>
    @else
    <h2 class="fw-bold mb-4 text-dark">📊 Customer Dashboard</h2>
    @endif

    <!-- KPI Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <h6 class="text-muted">Total Orders</h6>
                <h3 class="fw-bold text-primary">{{ $orderStats->sum() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <h6 class="text-muted">Top Product</h6>
                <h3 class="fw-bold text-success">
                    {{ $topProducts->first()->name ?? 'N/A' }}
                </h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <h6 class="text-muted">Active Customers</h6>
                <h3 class="fw-bold text-warning">{{ $activeCustomers->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <h6 class="text-muted">Pending Orders</h6>
                <h3 class="fw-bold text-danger">{{ $orderStats['pending'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Top Products & Customers -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent fw-bold text-dark">
                    <i class="bi bi-box-seam"></i> Top Selling Products
                </div>
                <div class="card-body">
                    @if($topProducts->count())
                        <ul class="list-group list-group-flush">
                            @foreach($topProducts as $product)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $product->name }}</span>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $product->orders_count ?? $product->order_items_count }} orders
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No product data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent fw-bold text-dark">
                    <i class="bi bi-people"></i> Most Active Customers
                </div>
                <div class="card-body">
                    @if($activeCustomers->count())
                        <ul class="list-group list-group-flush">
                            @foreach($activeCustomers as $customer)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $customer->name }}</span>
                                    <span class="badge bg-success rounded-pill">
                                        {{ $customer->orders_count }} orders
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No customer data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-transparent fw-bold text-dark">
                    <i class="bi bi-bar-chart"></i> Order Statistics
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($orderStats->toArray())) !!},
            datasets: [{
                label: 'Orders',
                data: {!! json_encode(array_values($orderStats->toArray())) !!},
                backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6c757d'],
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
