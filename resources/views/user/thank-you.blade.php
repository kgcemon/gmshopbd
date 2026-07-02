@extends('user.master')
@section('title', "Order Placed")
@section('content')

    <style>
        * { box-sizing: border-box; }

        .op-page { padding: 2rem 1rem; max-width: 560px; margin: 0 auto; }

        .op-header { text-align: center; margin-bottom: 2rem; }
        .op-check {
            width: 64px; height: 64px; border-radius: 50%;
            background: #d1fae5; display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 1rem; font-size: 30px; color: #059669;
        }
        .op-header h1 { font-size: 22px; font-weight: 600; color: #fff; }
        .op-header p  { font-size: 14px; color: #aaa; margin-top: 4px; }

        .op-card {
            background: #1e1e2e;
            border: 1px solid #2e2e42;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 12px;
        }
        .op-card-label {
            font-size: 11px; font-weight: 600; letter-spacing: .07em;
            text-transform: uppercase; color: #666; margin-bottom: 10px;
        }

        /* Product */
        .op-product { display: flex; align-items: center; gap: 14px; }
        .op-product img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; border: 1px solid #2e2e42; }
        .op-order-id { font-size: 12px; background: #2e2e42; color: #999; border-radius: 6px; padding: 2px 8px; display:inline-block; margin-bottom: 5px; }
        .op-pname  { font-size: 15px; font-weight: 600; color: #fff; }
        .op-pmeta  { font-size: 13px; color: #aaa; margin-top: 2px; }
        .op-total  { font-size: 16px; font-weight: 700; color: #4ade80; margin-top: 5px; }

        /* Player */
        .op-player { display: flex; align-items: center; gap: 14px; }
        .op-avatar { width: 50px; height: 50px; border-radius: 50%; background: #1e3a5f; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .op-nick   { font-size: 15px; font-weight: 600; color: #fff; }
        .op-badges { display: flex; gap: 8px; margin-top: 5px; flex-wrap: wrap; }
        .op-badge  { font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 99px; }
        .op-badge-lv { background: #3b2d00; color: #f59e0b; }
        .op-badge-rk { background: #0f2a3d; color: #38bdf8; }

        /* Loader */
        .op-loader-row { display: flex; align-items: center; gap: 10px; }
        .op-spinner { width: 20px; height: 20px; border-radius: 50%; border: 2px solid #333; border-top-color: #4e54c8; animation: op-spin .8s linear infinite; }
        @keyframes op-spin { to { transform: rotate(360deg); } }

        /* Info Grid */
        .op-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .op-ilabel { font-size: 12px; color: #666; margin-bottom: 2px; }
        .op-ivalue { font-size: 14px; font-weight: 600; color: #fff; }

        /* Status chips */
        .op-chip { display: inline-block; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 99px; }
        .op-chip-proc { background: #3b2500; color: #f59e0b; }
        .op-chip-done { background: #052e16; color: #4ade80; }
        .op-chip-pend { background: #1e1e2e; color: #888; border: 1px solid #333; }

        /* Payment */
        .op-payment { display: flex; align-items: center; gap: 14px; }
        .op-pay-icon { width: 44px; height: 44px; border-radius: 10px; object-fit: contain; background: #2e2e42; padding: 6px; border: 1px solid #3a3a52; }
        .op-pay-method { font-size: 15px; font-weight: 600; color: #fff; }
        .op-pay-sub    { font-size: 13px; color: #aaa; margin-top: 2px; }

        /* Support */
        .op-support-item { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .op-support-item:last-child { margin-bottom: 0; }
        .op-sup-icon { width: 40px; height: 40px; border-radius: 10px; background: #2e2e42; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .op-sup-title { font-size: 14px; font-weight: 600; color: #fff; }
        .op-sup-sub   { font-size: 13px; color: #aaa; margin-top: 2px; }
        .op-sup-sub a { color: #60a5fa; text-decoration: none; }
    </style>

    <div class="op-page">

        {{-- Header --}}
        <div class="op-header">
            <div class="op-check">✓</div>
            <h1>Order Placed!</h1>
            <p>আপনার অর্ডার সফলভাবে গ্রহণ করা হয়েছে</p>
        </div>

        {{-- Product --}}
        <div class="op-card">
            <div class="op-card-label">Product</div>
            <div class="op-product">
                <img src="/{{ $order->product->image }}" alt="Product">
                <div>
                    <div class="op-order-id">#{{ $order->id }}</div>
                    <div class="op-pname">{{ $order->item->name ?? $order->product->name }}</div>
                    <div class="op-total">৳ {{ $order->total }}</div>
                </div>
            </div>
        </div>

        {{-- Player Info --}}
        <div class="op-card">
            <div class="op-card-label">Player info</div>
            <div id="op-loading" class="op-loader-row">
                <div class="op-spinner"></div>
                <span style="font-size:14px;color:#888;">Fetching player info...</span>
            </div>
            <div id="op-player" class="op-player" style="display:none;">
                <div class="op-avatar">🎮</div>
                <div>
                    <div class="op-nick" id="nickname-text">--</div>
                    <div class="op-badges">
                        <span class="op-badge op-badge-lv">⭐ Level <span id="level-text">--</span></span>
                        <span class="op-badge op-badge-rk">🏆 <span id="rank-text">--</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="op-card">
            <div class="op-card-label">Customer info</div>
            <div class="op-grid">
                <div>
                    <div class="op-ilabel">{{ $order->product->input_name }}</div>
                    <div class="op-ivalue">{{ $order->customer_data }}</div>
                </div>
                <div>
                    <div class="op-ilabel">Status</div>
                    <div class="op-ivalue">
                        @if($order->status == 'delivered')
                            <span class="op-chip op-chip-done">✓ Delivered</span>
                        @elseif($order->status == 'processing')
                            <span class="op-chip op-chip-proc">⏳ Processing</span>
                        @else
                            <span class="op-chip op-chip-pend">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment --}}
        <div class="op-card">
            <div class="op-card-label">Payment</div>
            <div class="op-payment">
                <img class="op-pay-icon" src="{{ $order->paymentMethod->icon }}" alt="{{ $order->paymentMethod->method }}">
                <div>
                    <div class="op-pay-method">{{ $order->paymentMethod->method }}</div>
                    @if($order->paymentMethod->method != 'Wallet')
                        <div class="op-pay-sub">Number: {{ $order->transaction_id ?? '' }}</div>
                        <div class="op-pay-sub">TrxID: {{ $order->number ?? '' }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Support --}}
        <div class="op-card">
            <div class="op-card-label">Support</div>
            <div class="op-support-item">
                <div class="op-sup-icon">📞</div>
                <div>
                    <div class="op-sup-title">Call us</div>
                    <div class="op-sup-sub">01812556950</div>
                </div>
            </div>
            <div class="op-support-item">
                <div class="op-sup-icon">💬</div>
                <div>
                    <div class="op-sup-title">Facebook</div>
                    <div class="op-sup-sub"><a href="https://m.me/gmshopbd">m.me/gmshopbd</a></div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const uid = "{{ $order->customer_data }}";
            fetch(`/api/player-info/${uid}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById("op-loading").style.display = "none";
                    document.getElementById("nickname-text").textContent = data.nickname || "Not Found";
                    document.getElementById("level-text").textContent = data.level || "--";
                    document.getElementById("rank-text").textContent = data.rank || "--";
                    document.getElementById("op-player").style.display = "flex";
                })
                .catch(() => {
                    document.getElementById("op-loading").style.display = "none";
                    document.getElementById("nickname-text").textContent = "Error loading";
                    document.getElementById("op-player").style.display = "flex";
                });
        });
    </script>

@endsection
