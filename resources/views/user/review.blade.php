@extends('user.master')

@section('title', "Deposit")

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:'Inter',sans-serif;
            background:linear-gradient(135deg,#0F0C29 0%,#302B63 50%,#24243e 100%);
            min-height:100vh;overflow-x:hidden;position:relative;color:white;
        }
        .glow-orb{position:absolute;border-radius:50%;filter:blur(40px);pointer-events:none;z-index:-1}
        .glow-orb-1{width:250px;height:250px;background:radial-gradient(circle,rgba(0,212,255,0.25)0%,transparent 70%);top:20%;left:10%;animation:float-glow 15s infinite ease-in-out}
        .glow-orb-2{width:180px;height:180px;background:radial-gradient(circle,rgba(255,0,110,0.25)0%,transparent 70%);top:60%;right:15%;animation:float-glow 20s infinite ease-in-out reverse}
        @keyframes float-glow{0%,100%{transform:translate(0,0)scale(1)}25%{transform:translate(25px,-15px)scale(1.05)}50%{transform:translate(-15px,25px)scale(.95)}75%{transform:translate(15px,15px)scale(1.02)}}
        .container{max-width:500px;margin:0 auto;padding:25px 18px;text-align:center;position:relative;z-index:1}
        .header{margin-bottom:30px}
        .product-subtitle{font-size:1rem;color:rgba(255,255,255,.85);margin-bottom:18px;font-weight:400}
        h1 { font-size: 1rem !important; }

        .checkout-btn{width:100%;padding:14px;border:none;border-radius:16px;background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:600;cursor:pointer;font-size:1rem;transition:.3s}
        .checkout-btn:hover{background:linear-gradient(135deg,#059669,#047857)}

        .review-card {
            display: flex;
            gap: 12px;
            background: linear-gradient(145deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .review-avatar {
            flex: 0 0 50px;
            height: 50px;
            width: 50px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,.3);
        }

        .review-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .review-content { flex: 1; }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .review-name { font-weight: 600; font-size: 0.95rem; color: #fff; }
        .review-date { font-size: 0.75rem; color: rgba(255,255,255,.6); }

        .review-stars {
            color: gold;
            font-size: 1rem;
            margin-bottom: 6px; /* spacing between stars and text */
            display: block;
            text-align: left!important;
        }

        .review-text {
            font-size: 0.85rem;
            color: rgba(255,255,255,.9);
            line-height: 1.4;
            text-align: left!important;
        }
    </style>

    <body>
    <div class="glow-orb glow-orb-1"></div>
    <div class="glow-orb glow-orb-2"></div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Customer Reviews</h1>
            <p class="product-subtitle">What people are saying about this product</p>
        </div>

        @auth()
            <a href="/add-review/{{ request()->segment(2) }}">
                <button class="checkout-btn" style="margin-top:15px;">Add a Review</button>
            </a>
            <br>
            <br>
        @endauth

        <!-- Reviews -->
        @foreach($reviews as $review)
            <div class="review-card">
                <div class="review-avatar">
                    <img src="{{$review->user->image}}" alt="{{$review->user->name}}">
                </div>
                <div class="review-content">
                    <div class="review-header">
                        <span class="review-name">{{$review->user->name}}</span>
                        <span class="review-date">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</span>
                    </div>
                    <div class="review-stars">⭐ ⭐ ⭐ ⭐ ⭐</div>
                    <p class="review-text">{{$review->review}}</p>
                </div>
            </div>
        @endforeach

        <!-- Load More Button -->
        <button class="checkout-btn" style="margin-top:15px;">Load More Reviews</button>
    </div>
    </body>
@endsection
