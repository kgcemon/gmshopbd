@extends('user.master')

@section('title', "Privacy Policy Codzshop")

@section('content')
<main style="padding: 20px; padding-bottom: 80px;">
    <div class="panelData">
        <h1>Privacy Policy</h1>
        <p style="margin-top: 15px; line-height: 1.6;">
            At <strong>Codzshop.com</strong>, your privacy is very important to us. This Privacy Policy outlines how we collect, use, and protect your personal information when you use our Free Fire Diamonds auto delivery services.
        </p>

        <h2 style="margin-top: 20px;">Information We Collect</h2>
        <ul style="text-align: left; margin-top: 10px; line-height: 1.6;">
            <li>Account information such as email address and username</li>
            <li>Transaction details for purchases made on our platform</li>
            <li>Device and browser information for security purposes</li>
        </ul>

        <h2 style="margin-top: 20px;">How We Use Your Information</h2>
        <ul style="text-align: left; margin-top: 10px; line-height: 1.6;">
            <li>To provide and improve our auto delivery service</li>
            <li>To communicate important updates and promotions</li>
            <li>To ensure security and prevent fraud</li>
        </ul>

        <h2 style="margin-top: 20px;">Data Protection</h2>
        <p style="margin-top: 10px; line-height: 1.6;">
            We implement strict security measures to protect your personal information from unauthorized access, disclosure, or misuse. Your data is stored securely and only accessible by authorized personnel.
        </p>

        <h2 style="margin-top: 20px;">Third-Party Services</h2>
        <p style="margin-top: 10px; line-height: 1.6;">
            We do not share your personal information with third parties, except for trusted service providers necessary for transaction processing and system operations.
        </p>

        <h2 style="margin-top: 20px;">Changes to This Policy</h2>
        <p style="margin-top: 10px; line-height: 1.6;">
            We may update this Privacy Policy from time to time. We encourage you to review this page periodically for any changes.
        </p>

        <p style="margin-top: 15px; line-height: 1.6;">
            By using Codzshop.com, you agree to the terms of this Privacy Policy.
        </p>
    </div>
</main>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="/" class="nav-item">
        <span class="material-icons">home</span>
        <span class="nav-label">Home</span>
    </a>
    <a href="/shop" class="nav-item">
        <span class="material-icons">shopping_cart</span>
        <span class="nav-label">Shop</span>
    </a>
    <a href="/about" class="nav-item">
        <span class="material-icons">info</span>
        <span class="nav-label">About</span>
    </a>
    <a href="/privacy" class="nav-item active">
        <span class="material-icons">privacy_tip</span>
        <span class="nav-label">Privacy</span>
    </a>
</div>
@endsection
