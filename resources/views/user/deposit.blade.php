@extends('user.master')

@section('title', "Deposit")

@section('content')
    <style>
        .body {
            margin: 15px;
        }
        .payment-option.selected {
            border: 2px solid #007bff;
            background: #f0f8ff;
        }
        /* Simple toast style */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f44336;
            color: #fff;
            padding: 10px 15px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 9999;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }
        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .toast.success {
            background: #4CAF50;
        }
    </style>

    <!-- Payment Selection -->
    <br>

    <div class="selection-panel body">
        <img src="wallet.png" alt="wallet" style="height:25px; margin-right:5px;"> {{$amount}}৳
    </div>
    <br>

    <div class="selection-panel body" id="step3">
        <h2 class="selection-title">পেমেন্ট পদ্ধতি নির্বাচন করুন</h2>
        <div class="payment-methods" style="display:flex; flex-wrap:wrap; gap:10px;">
            @foreach($payment as $method)
                <div class="payment-option
                     @if($loop->first) selected @endif"
                     style="flex:1 1 calc(33.333% - 10px); padding:10px; border:1px solid #ccc; border-radius:8px; cursor:pointer;"
                     data-id="{{ $method->id }}"
                     data-number="{{ $method->number }}"
                     data-method="{{ $method->method }}"
                     data-description="{{ $method->description }}">
                    <img src="{{ $method->icon }}" alt="{{ $method->method }}" style="height:25px; margin-right:5px;">
                </div>
            @endforeach
        </div>

        <div class="payment-details" id="paymentDetails"></div>

        <!-- Transaction ID input -->
        <div id="trxBox" class="player-id-box" style="display:none;">
            <h2 class="selection-title">Transaction ID লিখুন</h2>
            <input type="text" id="trxId" placeholder="Enter Transaction ID">
        </div>

        <!-- Payment Number input -->
        <div id="paymentNumberBox" class="player-id-box" style="display:none;">
            <h2 class="selection-title">Payment Number লিখুন</h2>
            <input type="text" id="paymentNumber" placeholder="Enter Payment Number">
        </div>
        <br>
        <input type="hidden" name="amount" value="{{$amount}}">
        <!-- Submit Button -->
        <button class="checkout-btn" id="checkoutBtn">Submit Order</button>
    </div>

    <!-- Toast container -->
    <div id="toast" class="toast"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let selectedPayment = null;

            const paymentOptions = document.querySelectorAll(".payment-option");
            const trxBox = document.getElementById("trxBox");
            const trxIdInput = document.getElementById("trxId");
            const paymentNumberBox = document.getElementById("paymentNumberBox");
            const paymentNumberInput = document.getElementById("paymentNumber");
            const checkoutBtn = document.getElementById("checkoutBtn");
            const toast = document.getElementById("toast");

            // hidden input থেকে amount নিব
            const depositAmount = "{{ $amount }}";

            // Toast function
            function showToast(message, type = "error") {
                toast.textContent = message;
                toast.className = `toast show ${type === "success" ? "success" : ""}`;
                setTimeout(() => {
                    toast.className = toast.className.replace("show", "");
                }, 3000);
            }

            // পেমেন্ট সিলেক্ট হলে UI সেট করব
            function selectPayment(el) {
                paymentOptions.forEach(o => o.classList.remove("selected"));
                el.classList.add("selected");

                selectedPayment = {
                    id: parseInt(el.dataset.id, 10),
                    method: el.dataset.method,
                    number: el.dataset.number,
                    description: el.dataset.description
                };

                document.getElementById("paymentDetails").innerHTML = `
                <p><strong>${selectedPayment.method}</strong></p>
                <p><strong>Number:</strong> ${selectedPayment.number}</p>
                <br><p>${selectedPayment.description}</p><br>
            `;

                if (selectedPayment.method === "Wallet") {
                    trxBox.style.display = "none";
                    paymentNumberBox.style.display = "none";
                } else {
                    trxBox.style.display = "block";
                    paymentNumberBox.style.display = "none";
                }
            }

            // Auto select first payment method
            if (paymentOptions.length > 0) {
                selectPayment(paymentOptions[0]);
            }

            // অন্য অপশন ক্লিক করলে
            paymentOptions.forEach(el => {
                el.addEventListener("click", () => selectPayment(el));
            });

            // Submit
            checkoutBtn.addEventListener("click", () => {
                if (!selectedPayment) {
                    showToast("অনুগ্রহ করে একটি পেমেন্ট মেথড নির্বাচন করুন!");
                    return;
                }

                const trxId = trxIdInput.value.trim();
                const payNumber = paymentNumberInput.value.trim();

                if (selectedPayment.method !== "Wallet") {
                    if (trxId.length < 5) {
                        showToast("Valid Transaction ID দিন!");
                        trxIdInput.focus();
                        return;
                    }
                }

                const formData = new FormData();
                formData.append("amount", depositAmount);
                formData.append("payment_id", selectedPayment.id);
                formData.append("transaction_id", trxId);
                formData.append("payment_number", payNumber);

                checkoutBtn.disabled = true;
                checkoutBtn.innerText = "Processing...";

                fetch("{{ url('/add-money') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                })
                    .then(async res => {
                        const data = await res.json();
                        checkoutBtn.disabled = false;
                        checkoutBtn.innerText = "Submit Order";

                        if (res.status === 422) {
                            paymentNumberBox.style.display = "block";
                            paymentNumberInput.focus();
                            showToast("Payment Number is required!", "error");
                            return;
                        }

                        if (res.status === 409) {
                            showToast("This transaction ID is already used.", "error");
                            return;
                        }

                        if (data.status) {
                            showToast("✅ ডিপোজিট সফল হয়েছে!", "success");
                            window.location.href = "/thank-you/"+ data.order.uid;
                        } else {
                            paymentNumberBox.style.display = "block";
                            paymentNumberInput.focus();
                            showToast("❌ ব্যর্থ: " + data.message, "error");
                        }
                    })
                    .catch(() => {
                        checkoutBtn.disabled = false;
                        checkoutBtn.innerText = "Submit Order";
                        showToast("⚠️ সার্ভার এরর, আবার চেষ্টা করুন।", "error");
                    });
            });
        });
    </script>
@endpush
