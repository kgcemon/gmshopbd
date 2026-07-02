@extends('user.master')

@section('title', "About Codzshop")

@section('content')
<!-- Main Content -->
<main style="padding: 20px; padding-bottom: 80px;">
    <div class="panelData">
        <h1>About Codzshop.com</h1>
        <p style="margin-top: 15px; line-height: 1.6;">
            Welcome to <strong>Codzshop.com</strong> – your trusted platform for <strong>Free Fire Diamonds</strong>!
            We provide a fast, reliable, and fully automated delivery system for all your Free Fire Diamond purchases.
            Our goal is to make sure you receive your diamonds instantly, safely, and securely, so you can enjoy your game without any delays.
        </p>

        <h2 style="margin-top: 20px;">Why Choose Us?</h2>
        <ul style="text-align: left; margin-top: 10px; line-height: 1.6;">
            <li>Instant auto delivery of Free Fire Diamonds</li>
            <li>Safe and secure transactions</li>
            <li>User-friendly interface</li>
            <li>24/7 support for all your queries</li>
        </ul>

        <h2 style="margin-top: 20px;">Our Mission</h2>
        <p style="margin-top: 10px; line-height: 1.6;">
            To provide gamers with a seamless and enjoyable experience when purchasing Free Fire Diamonds, ensuring trust, speed, and convenience at every step.
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
    <a href="/about" class="nav-item active">
        <span class="material-icons">info</span>
        <span class="nav-label">About</span>
    </a>
    <a href="/account" class="nav-item">
        <span class="material-icons">account_circle</span>
        <span class="nav-label">Account</span>
    </a>
</div>
@endsection
