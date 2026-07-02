@extends('user.master')

@section('title', 'Order Details')

@section('content')
    <div class="container py-3">

        <!-- Order Info Card -->
        <div class="order-detail-card">
            <div class="order-row">
                <span class="label">Order ID:</span>
                <span class="value">#{{ $order->id }}</span>
            </div>
            <div class="order-row">
                <span class="label">Date:</span>
                <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="order-row">
                <span class="label">{{$order->product->input_name}}</span>
                <span class="value">{{ $order->customer_data }}</span>
            </div>
            <div class="order-row">
                <span class="label">Total:</span>
                <span class="value">{{ number_format($order->total,2) }} ৳</span>
            </div>
            <div class="order-row">
                <span class="label">Status:</span>
                <span class="value status {{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <!-- Ordered Items -->
        <div class="items-section">
            <h3 class="section-title">Items</h3>
                <div class="item-card">
                    <img src="/{{ $order->product->image }}" alt="{{ $order->product->name }}">
                    <div class="item-info">
                        <span class="item-name">{{ $order->item->name ?? $order->product->name }}</span>
                        <span class="item-qty">Qty: {{ $order->quantity }}</span>
                    </div>
                    <div class="item-price">{{ $order->item->price ?? $order->total }} ৳</div>
                </div>
        </div>
        <br>
        <br>
        <div class="items-section">
            <div class="item-card">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="24" height="24"
                     fill="white"
                     viewBox="0 0 24 24">
                    <path d="M20 2H4C2.9 2 2 2.9 2 4v14c0
           1.1.9 2 2 2h14l4 4V4c0-1.1-.9-2-2-2zm-2
           12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                </svg>
                <div class="item-info">
                    <span class="item-qty">{{ $order->order_note ?? "No message" }}</span>
                </div>

            </div>
        </div>

        <br>
        <div class="items-section">
            <h3 class="section-title">Payment</h3>
            <div class="item-card">
                <img src="{{ $order->paymentMethod->icon }}" alt="{{ $order->paymentMethod->method }}" height="40" width="40">
                <div class="item-info">
                    <span class="item-name">{{ $order->paymentMethod->method }}</span>
                    <span class="item-qty">{{ $order->transaction_id ." " . $order->number }}</span>
                </div>
                @php
                    switch ($order->status) {
                        case 'hold':
                            $paymentStatus = 'Pending';
                            break;

                        case 'processing':
                        case 'Delivery Running':
                        case 'delivered':
                            $paymentStatus = 'Paid';
                            break;

                        case 'cancelled':
                            $paymentStatus = 'Unpaid';
                            break;

                        default:
                            $paymentStatus = ucfirst($order->status);
                            break;
                    }
                @endphp

                <span class="value status {{ strtolower($paymentStatus) }}">
    {{ $paymentStatus }}
</span>

            </div>
        </div>

    </div>
@endsection

<style>
    /* Container */
    .container {
        max-width: 480px;
        margin: 0 auto;
        padding: 15px;
    }

    /* Order Detail Card */
    .order-detail-card {
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 5px;
        padding: 15px 20px;
        color: #fff;
        margin: 10px 0;
    }

    .order-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .order-row .label {
        color: #ccc;
        font-weight: 500;
    }

    .order-row .value {
        font-weight: 600;
        color: #fff;
    }

    /* Status Badge */
    .status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        text-align: center;
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


    .status.Pending {
        background: #ffc10733;
        color: #ffc107;
    }


    .status.Paid {
        background: #28a74533;
        color: #28a745;
    }
    .status.Unpaid {
        background: #dc354533;
        color: #dc3545;
    }

    /* Items Section */
    .items-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .items-section .section-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: #fff;
    }

    /* Item Card */
    .item-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(10px);
        border-radius: 5px;
        padding: 10px 15px;
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.3);
    }

    .item-card img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }

    .item-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        font-size: 13px;
    }

    .item-name {
        font-weight: 600;
        color: #fff;
    }

    .item-qty {
        font-size: 12px;
        color: #ccc;
    }

    .item-price {
        font-weight: 600;
        color: #00d4ff;
    }

    /* Responsive */
    @media(max-width:480px){
        .order-detail-card, .item-card {
            padding: 12px 15px;
        }
        .item-card img {
            width: 45px;
            height: 45px;
        }
    }
</style>
