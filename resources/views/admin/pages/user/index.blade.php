@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid mt-4">

        {{-- Card --}}
        <div class="card shadow border-0 rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-primary">👥 Manage Users</h5>

                {{-- Search Form --}}
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-100 w-md-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control rounded-start-pill"
                               placeholder="Search by name or email..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary rounded-end-pill px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">

                {{-- Session Messages --}}
                <div class="px-3 pt-3">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 py-2 shadow-sm" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-pill px-4 py-2 shadow-sm" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>

                {{-- User Table --}}
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Wallet</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr class="hover-row">
                                <td>{{ $loop->iteration }}</td>

                                {{-- User Image --}}
                                <td>
                                    <img src="{{ $user->image ?? asset('default-avatar.png') }}"
                                         class="rounded-circle shadow-sm"
                                         width="45" height="45"
                                         alt="User Image">
                                </td>

                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td class="text-muted small">{{ $user->email }}</td>
                                <td><a href="user-transaction/{{$user->id}}">{{ number_format($user->wallet ?? 0, 2) }}৳</a></td>
                                <td class="text-muted small">{{ $user->created_at ? $user->created_at->diffForHumans() : 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning rounded-pill shadow-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-wallet="{{ $user->wallet ?? 0 }}">
                                        <i class="fas fa-edit text-white"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No users found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="py-3 d-flex justify-content-center">
                    {{ $users->links('admin.layouts.partials.__pagination') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-primary">Edit User Details</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body px-4">
                        <input type="hidden" name="id" id="editUserId">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control rounded-pill" id="editUserName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control rounded-pill" id="editUserEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Wallet Balance (৳)</label>
                            <input type="number" step="0.01" class="form-control rounded-pill" id="editUserWallet" name="wallet">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (Optional)</label>
                            <input type="password" class="form-control rounded-pill" id="editUserPassword" name="password" placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .hover-row:hover {
            background: #f8f9fa !important;
            transition: 0.2s ease-in-out;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editUserModal = document.getElementById('editUserModal');
            editUserModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var userId = button.getAttribute('data-id');
                var userName = button.getAttribute('data-name');
                var userEmail = button.getAttribute('data-email');
                var userWallet = button.getAttribute('data-wallet');

                var modalForm = document.getElementById('editUserForm');
                modalForm.action = `/admin/users/${userId}`;

                document.getElementById('editUserId').value = userId;
                document.getElementById('editUserName').value = userName;
                document.getElementById('editUserEmail').value = userEmail;
                document.getElementById('editUserWallet').value = userWallet;
                document.getElementById('editUserPassword').value = '';
            });
        });
    </script>
@endpush
