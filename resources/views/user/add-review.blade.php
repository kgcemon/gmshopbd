@extends('user.master')

@section('title', "Product Review")

@section('content')
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

        .selection-panel{
            background:linear-gradient(145deg,rgba(255,255,255,.08),rgba(255,255,255,.04));
            backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,.2);
            border-radius:8px;
            padding:25px 20px 20px 20px;
            margin-bottom:25px;
            text-align:left;
            position:relative;
            transition: .3s;
        }
        .selection-panel::before{
            content: attr(data-step);
            position:absolute;
            top:-15px;
            left:20px;
            background:#6b7280;
            color:white;
            width:30px;
            height:30px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            font-size:1rem;
            transition:.3s;
        }

        .selection-panel.completed::before{
            content:"✓";
            background:#10b981;
        }
        .selection-title{font-size:1rem;color:white;margin-bottom:15px;font-weight:600;text-align: left!important;}

        .des{
            background:linear-gradient(145deg,rgba(255,255,255,.08),rgba(255,255,255,.04));
            backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,.2);
            border-radius:8px;
            padding:25px 20px 20px 20px;
            margin-bottom:25px;
            text-align:left;
            position:relative;
            transition: .3s;
        }

        .li{
            padding-bottom: 2px;
        }

        .player-id-box label{color:white;display:block;margin-bottom:8px;font-weight:500;font-size:.9rem}
        .player-id-box input{width:100%;padding:12px 16px;border-radius:12px;border:2px solid rgba(255,255,255,.1);background:rgba(255,255,255,.1);color:white;font-size:.9rem;outline:none}
        .player-id-box input:focus{border-color:#00d4ff;box-shadow:0 0 12px rgba(0,212,255,.25)}
        .error-message{color:#ff6b6b;font-weight:500;font-size:.85rem;margin-top:8px;text-align:left}

        .diamond-options{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:12px;
        }
        .diamond-option{
            background:linear-gradient(145deg,rgba(255,255,255,.07),rgba(255,255,255,.03));
            border:2px solid rgba(255,255,255,.1);
            border-radius:8px;
            padding:12px 8px;
            cursor:pointer;
            transition:.3s;
            color:white;
            font-size:.7rem;
            font-weight:500;
            justify-content:space-around;
            align-items:center;
            position:relative;
            text-align: center;
        }
        .diamond-option .price{
            font-size:.63rem;
            font-weight:500;
            opacity:.85;
            position: relative;
            top: -5px;
            color: #00d4ff;
        }
        .diamond-option.selected{
            /*background:linear-gradient(135deg,#00d4ff,#090979);*/
            border-color:#00d4ff;
            animation:pulse 2s infinite;
        }
        .diamond-option.selected::after{
            content:'✓';
            position:absolute;
            top:8px;
            right:10px;
            font-size:1rem;
            color:#10b981;
            font-weight:bold;
            justify-content: left;
        }

        h1 {
            font-size: 1rem !important;
        }




        @keyframes pulse{0%,100%{box-shadow:0 0 15px rgba(0,212,255,.4)}50%{box-shadow:0 0 15px rgba(0,212,255,.6)}}

        /* Payment Methods */
        .payment-methods{display:flex;gap:10px}
        .payment-option{flex:1;padding:12px 10px;border-radius:14px;border:2px solid rgba(255,255,255,.2);background:linear-gradient(145deg,rgba(255,255,255,.07),rgba(255,255,255,.03));color:white;cursor:pointer;text-align:center;transition:.3s;font-size:.9rem}
        .payment-option.selected{background:linear-gradient(135deg,#10b981,#059669);border-color:#10b981}
        .payment-details{margin-top:12px;color:white;font-size:.85rem;line-height:1.4}

        /* Checkout button */
        .checkout-btn{width:100%;padding:14px;border:none;border-radius:16px;background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:600;cursor:pointer;font-size:1rem;transition:.3s}
        .checkout-btn:hover{background:linear-gradient(135deg,#059669,#047857)}

        .product-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background:linear-gradient(145deg,rgba(255,255,255,.08),rgba(255,255,255,.04));
            backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,.2);
            border-radius:8px;
            margin-bottom:25px;
            text-align:left;
            position:relative;
            transition: .3s;
        }


        .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: fill;
            border-radius: 10px;
        }

        .product-details {
            flex-direction: column;
            justify-content: center;
        }

        .product-subtitle {
            font-size: 0.85rem;
            color: white;
            margin-top: 3px;
        }

        /* Responsive */
        @media (max-width: 500px) {
            .product-card {
                max-width: 100%;
                padding: 8px 10px;
            }
            .product-thumb {
                flex: 0 0 80px;
                height: 80px;
            }

            .product-subtitle {
                font-size: 0.6rem;
            }
        }

        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }
        .btn-loading::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 50%;
            width: 18px;
            height: 18px;
            margin-top: -9px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .response-box {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
        }
        .response-box.success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .response-box.error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }

        /* Add inside <style> */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }
        .loading-spinner .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }




        .payment-methods {
            display: flex;
            flex-wrap: wrap; /* অনেক হলে নিচে wrap হবে */
            gap: 12px;       /* spacing */
            margin-top: 15px;
        }

        .payment-option {
            padding: 10px 15px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            display: inline-flex; /* auto width */
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.2s ease-in-out;
            box-sizing: border-box;
        }

        .stars span {
            font-size: 2rem;
            cursor: pointer;
            color: #ccc;
            transition: .2s;
        }
        .stars span.selected,
        .stars span.hover {
            color: gold;
        }
    </style>

    <div class="container">
        <div class="header">
            <h1>Product Review</h1>
            <p class="product-subtitle">Share your feedback about this item</p>
        </div>

        <div class="response-box success" style="display:none"></div>
        <div class="response-box error" style="display:none"></div>

        <!-- Product Card -->
        <div class="product-card">
            <div class="product-thumb">
                <img src="/{{$product->image}}" alt="Product">
            </div>
            <div class="product-details">
                <h3>{{$product->name}}</h3>
                <p class="product-subtitle">{{$product->seo_description}}</p>
            </div>
        </div>

        <!-- Rating -->
        <div class="selection-panel" data-step="1">
            <div class="selection-title">Rate this Product</div>
            <div class="stars" id="rating-stars">
                <span data-value="1">★</span>
                <span data-value="2">★</span>
                <span data-value="3">★</span>
                <span data-value="4">★</span>
                <span data-value="5">★</span>
            </div>
            <input type="hidden" id="rating" value="">
        </div>

        <!-- Review -->
        <div class="selection-panel" data-step="2">
            <div class="selection-title">Write Your Review</div>
            <div class="player-id-box">
            <textarea id="review"
                      style="width:100%;padding:12px 16px;border-radius:12px;
                 border:2px solid rgba(255,255,255,.1);
                 background:rgba(255,255,255,.1);
                 color:white;font-size:.9rem;outline:none;
                 min-height:100px;resize:none;"
                      placeholder="Write your experience here..."></textarea>
            </div>
        </div>

        <!-- Submit -->
        <button class="checkout-btn" id="submitReview">Submit Review</button>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loading">
        <div class="spinner"></div>
        <p>Submitting your review...</p>
    </div>

    <br>
    <br>
    <br>

    <script>
        const stars = document.querySelectorAll('#rating-stars span');
        const ratingInput = document.getElementById('rating');
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const value = parseInt(star.dataset.value);
                stars.forEach(s => {
                    s.classList.toggle('hover', parseInt(s.dataset.value) <= value);
                });
            });

            star.addEventListener('mouseout', () => {
                stars.forEach(s => s.classList.remove('hover'));
            });

            star.addEventListener('click', () => {
                const value = parseInt(star.dataset.value);
                stars.forEach(s => {
                    s.classList.toggle('selected', parseInt(s.dataset.value) <= value);
                });
                ratingInput.value = value;
            });
        });
        // Submit Review
        document.getElementById('submitReview').addEventListener('click', async () => {
            const review = document.getElementById('review').value.trim();
            const rating = ratingInput.value;
            const productId = "{{$product->slug}}";

            const loading = document.getElementById('loading');
            const successBox = document.querySelector('.response-box.success');
            const errorBox = document.querySelector('.response-box.error');

            successBox.style.display = 'none';
            errorBox.style.display = 'none';

            if (!rating) {
                errorBox.textContent = "Please select a rating.";
                errorBox.style.display = "block";
                return;
            }
            if (!review) {
                errorBox.textContent = "Please write a review.";
                errorBox.style.display = "block";
                return;
            }

            loading.style.display = 'flex';

            try {
                const res = await fetch("{{route('review.store')}}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{csrf_token()}}"
                    },
                    body: JSON.stringify({
                        review: review,
                        rating: rating,
                        product_id: productId
                    })
                });
                const data = await res.json();
                loading.style.display = 'none';

                if (data.success) {
                    successBox.textContent = data.message;
                    successBox.style.display = "block";
                    document.getElementById('review').value = "";
                    ratingInput.value = "";
                    stars.forEach(s => s.classList.remove('selected'));
                } else {
                    errorBox.textContent = data.message;
                    errorBox.style.display = "block";
                }
            } catch (err) {
                loading.style.display = 'none';
                errorBox.textContent = "Something went wrong!";
                errorBox.style.display = "block";
            }
        });
    </script>
@endsection
