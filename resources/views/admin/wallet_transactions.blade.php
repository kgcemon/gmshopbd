@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="mb-0">💰 Wallet Transactions</h6>
                <span class="badge bg-success mt-2 mt-sm-0">
                    Balance: {{ number_format($balance, 2) }} ৳
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 text-center align-middle small">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions as $key => $txn)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="text-nowrap">{{ $txn->created_at->format('d M Y h:i A') }}</td>
                                <td>
                                    @if($txn->type == 'credit')
                                        <span class="badge bg-success">Credit</span>
                                    @else
                                        <span class="badge bg-danger">Debit</span>
                                    @endif
                                </td>
                                <td>
                                    @if($txn->type == 'credit')
                                        <span class="text-success fw-bold">+ {{ number_format($txn->amount, 2) }} ৳</span>
                                    @else
                                        <span class="text-danger fw-bold">- {{ number_format($txn->amount, 2) }} ৳</span>
                                    @endif
                                </td>
                                <td>
                                    @if($txn->status == 0)
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($txn->status == 1 )
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($txn->status === 2)
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 120px;">{{ $txn->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted py-4">No transactions found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <style>
        /* ছোট ফন্ট এবং মোবাইল ভিউ */
        .table th, .table td {
            font-size: 13px;
            padding: 6px;
        }
        .card-header h6 {
            font-size: 15px;
        }
        @media (max-width: 576px) {
            .table th, .table td {
                font-size: 12px;
            }
            .badge {
                font-size: 11px;
                padding: 4px 6px;
            }
        }
    </style>
@endsection
