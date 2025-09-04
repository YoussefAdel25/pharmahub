@extends('layouts.app')

@section('content')
<h1>My Orders</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th><th>Status</th><th>Items</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            <td>#{{ $order->id }}</td>
            <td>{{ $order->status }}</td>
            <td>
                <ul>
                    @foreach ($order->items as $item)
                        <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
