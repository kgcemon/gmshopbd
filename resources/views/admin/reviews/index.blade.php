@extends('admin.layouts.app')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <!-- Success & Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Search & Filter -->
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by User or Product..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}" {{ request('rating')==$i ? 'selected' : '' }}>{{ $i }} Star</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <!-- Reviews Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Review</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->user->name ?? 'N/A' }}</td>
                            <td>{{ $review->product->name ?? 'N/A' }}</td>
                            <td style="font-size: 12px">{{ $review->review ?? '-' }}</td>
                            <td>{{ $review->rating }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editReviewModal"
                                        data-id="{{ $review->id }}"
                                        data-review="{{ $review->review }}"
                                        data-rating="{{ $review->rating }}"
                                    >Edit</button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteReviewModal"
                                        data-id="{{ $review->id }}"
                                        data-name="{{ $review->user->name ?? 'Unknown' }}"
                                    ><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No reviews found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $reviews->appends(request()->query())->links('admin.layouts.partials.__pagination') }}
            </div>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editReviewForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Review</label>
                            <textarea class="form-control" id="editReviewText" name="review"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select class="form-select" id="editReviewRating" name="rating" required>
                                @for($i=1;$i<=5;$i++)
                                    <option value="{{ $i }}">{{ $i }} Star</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Review</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Review Modal -->
    <div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteReviewForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this review from "<strong id="deleteReviewUser"></strong>"?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit
            const editModal = document.getElementById('editReviewModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const review = button.getAttribute('data-review');
                const rating = button.getAttribute('data-rating');

                const form = document.getElementById('editReviewForm');
                form.action = `/admin/reviews/${id}`;

                document.getElementById('editReviewText').value = review;
                document.getElementById('editReviewRating').value = rating;
            });

            // Delete
            const deleteModal = document.getElementById('deleteReviewModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                const form = document.getElementById('deleteReviewForm');
                form.action = `/admin/reviews/${id}`;
                document.getElementById('deleteReviewUser').textContent = name;
            });
        });
    </script>
@endpush
