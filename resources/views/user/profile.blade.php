@extends('user.master')

@section('title', "Profile")

@section('content')
    <div style="max-width: 420px; margin: 0px auto; padding: 20px; border-radius: 0px;
    background: rgba(48,43,99,0.9);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    color: #fff;
    font-family: 'Inter', Arial, sans-serif;">

        <!-- Profile Header -->
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
            <img src="{{ Auth::user()->image }}"
                 alt="Profile Picture"
                 style="width: 70px; height: 70px; border-radius: 50%;
                    object-fit: cover;
                    border: 3px solid #00d4ff;
                    box-shadow: 0 0 10px rgba(0,212,255,0.7);">
            <div>
                <div style="font-weight: 700; font-size: 20px;">{{ Auth::user()->name ?? 'User Name' }}</div>
                <div style="font-size: 14px; color: #ccc;">{{ Auth::user()->email ?? 'user@example.com' }}</div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div style="display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; margin-bottom: 10px;">
            <div class="panelData" style="padding: 14px; border-radius: 12px;
             background: rgba(255,255,255,0.05);
             text-align: center;
             transition: all .3s;">
                <div style="font-size: 13px; color: #aaa;">Total Orders</div>
                <div style="font-weight: 700; font-size: 18px;">{{ $totalOrders ?? 0 }}</div>
            </div>
            <div class="panelData" style="padding: 14px; border-radius: 12px;
             background: rgba(255,255,255,0.05);
             text-align: center;
             transition: all .3s;">
                <div style="font-size: 13px; color: #aaa;">Completed</div>
                <div style="font-weight: 700; font-size: 18px;">{{ $completedOrders ?? 0 }}</div>
            </div>
            <div class="panelData" style="padding: 14px; border-radius: 12px;
             background: rgba(255,255,255,0.05);
             text-align: center;
             transition: all .3s;">
                <div style="font-size: 13px; color: #aaa;">Referral Income</div>
                <div style="font-weight: 700; font-size: 18px; color:#00d4ff;">{{ $refIncome ?? 0 }}৳</div>
            </div>
            <div class="panelData" style="padding: 14px; border-radius: 12px;
             background: rgba(255,255,255,0.05);
             text-align: center;
             transition: all .3s;">
                <div style="font-size: 13px; color: #aaa;">Total Referrals</div>
                <div style="font-weight: 700; font-size: 18px;">{{ $totalReferrals ?? 0 }}</div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div>
            <div style="font-weight: 600; font-size: 17px; margin-bottom: 12px;">Recent Transactions</div>
            <div style="overflow-y: auto; max-height: 400px;">
                @forelse($recentTransactions as $txn)
                    <div class="panelData" style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 14px;
                margin-bottom: 10px;
                border-radius: 12px;
                background: rgba(255,255,255,0.05);
                transition: all .3s;
                border-left: 4px solid {{ $txn->type == 'credit' ? '#28a745' : '#dc3545' }};
            ">
                        <div style="flex:1;">
                            <div style="font-size: 13px; color: #aaa;">{{ $txn->description }}</div>
                            <div style="font-size: 12px; color: #888;">{{ $txn->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; font-size: 15px;
                                color: {{ $txn->type == 'credit' ? '#28a745' : '#dc3545' }};">
                                {{ $txn->type == 'credit' ? '+' : '-' }}{{ number_format($txn->amount, 2) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #bbb; font-size: 14px; text-align:center;">No recent transactions found.</p>
                @endforelse
            </div>
        </div>

    </div>
    <br>
    <br>

    <!-- Small CSS animations -->
    <style>
        .panelData:hover {
            transform: scale(1.04);
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 10px;
        }
    </style>
@endsection
