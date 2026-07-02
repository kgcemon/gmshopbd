<div class="card shadow border-0 rounded-4">
    <div class="card-body table-responsive p-0">

        <table class="table table-hover align-middle text-center mb-0">
            <thead class="bg-primary text-white">
            <tr>
                <th>Amount</th>
                <th>Sender</th>
                <th>Number</th>
                <th>Transaction</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($data as $sms)
                <tr class="hover-row">
                    <td class="fw-bold text-success">{{ $sms->amount }}৳</td>
                    <td>{{ $sms->sender }}</td>
                    <td>{{ $sms->number }}</td>
                    <td class="text-muted small">{{ $sms->trxID }}</td>
                    <td>
                        @php
                            $statusText = $sms->status == 0 ? 'Pending' : ($sms->status == 1 ? 'Completed' : 'Failed');
                            $statusClass = $sms->status == 0 ? 'bg-warning text-dark' : ($sms->status == 1 ? 'bg-success' : 'bg-danger');
                        @endphp
                        <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $sms->created_at ? $sms->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">

                            <form action="{{ route('admin.sms.delete', $sms->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm"
                                        onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-muted text-center py-4">No SMS found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    <div class="py-3 d-flex justify-content-center">
        {{ $data->links('admin.layouts.partials.__pagination') }}
    </div>
</div>
