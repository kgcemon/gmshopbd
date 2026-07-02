@extends('user.master')

@section('title', 'My Orders')

@section('content')
    <div class="container py-4">

        <!-- Orders Table -->
        <div class="order-history">
            <table class="order-table">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{$order->item->name ?? $order->product->name}}</td>
                        <td>
                        <span class="status {{ strtolower($order->status) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        </td>
                        <td>
                             <a href="{{ route('orderView', $order->id) }}" class="btn-view">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No orders found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="order-history">
            {{ $orders->links('admin.layouts.partials.__pagination') }}
        </div>
    </div>
@endsection

<style>
    .order-history {
        overflow-x: auto;
        background: linear-gradient(145deg, #1e1b38, #252250);
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        padding: 10px;
    }
    .order-table {
        width: 100%;
        border-collapse: collapse;
    }
    .order-table thead {
        background: #2c2a4a;
    }
    .order-table th, .order-table td {
        padding: 14px 16px;
        text-align: center;
        font-size: 14px;
        border-bottom: 1px solid #3a3960;
    }
    .order-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        color: #ccc;
    }
    .order-table td {
        color: #fff;
    }
    .status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        display: inline-block;
    }
    .status.hold {
        background: #ffc10733;
        color: #ffc107;
    }
    .status.processing {
        background: #28a74533;
        color: #28a745;
    }
    .status.delivered {
        background: #28a74533;
        color: #28a745;
    }
    .status.cancelled {
        background: #dc354533;
        color: #dc3545;
    }
    .btn-view {
        display: inline-block;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        background: #00d4ff;
        color: #000;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-view:hover {
        background: #00a6cc;
        color: #fff;
    }

    /* Scrollbar dark theme */
    .order-history::-webkit-scrollbar {
        height: 8px;
    }
    .order-history::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 4px;
    }
    .order-history::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Responsive */
    @media (max-width: 600px) {
        .order-table th, .order-table td {
            font-size: 12px;
            padding: 10px 8px;
        }
        .status {
            font-size: 11px;
            padding: 3px 8px;
        }
    }
</style>
