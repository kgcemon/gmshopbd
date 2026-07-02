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

            <!-- Add Button -->
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addHelpLineModal">
                    Add New Help Line
                </button>
            </div>

            <!-- Helpline Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lines as $line)
                        <tr>
                            <td>{{ $line->id }}</td>
                            <td>{{ $line->name }}</td>
                            <td>
                                @if($line->image)
                                    <img src="{{ asset('uploads/helpline/'.$line->image) }}" alt="" width="40">
                                @endif
                            </td>
                            <td><a href="{{ $line->url }}" target="_blank">{{ $line->url }}</a></td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editHelpLineModal"
                                            data-id="{{ $line->id }}"
                                            data-name="{{ $line->name }}"
                                            data-url="{{ $line->url }}">
                                        Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteHelpLineModal"
                                            data-id="{{ $line->id }}"
                                            data-name="{{ $line->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No help lines found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $lines->links('admin.layouts.partials.__pagination') }}
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addHelpLineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.helpline.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Help Line</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL</label>
                            <input type="url" name="url" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Help Line</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editHelpLineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editHelpLineForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Help Line</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editHelplineId" name="id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" id="editHelplineName" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL</label>
                            <input type="url" id="editHelplineUrl" name="url" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Help Line</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteHelpLineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteHelpLineForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Help Line</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete "<strong id="deleteHelpLineName"></strong>"?</p>
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
            // Edit Modal
            const editModal = document.getElementById('editHelpLineModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const url = button.getAttribute('data-url');

                const form = document.getElementById('editHelpLineForm');
                form.action = `/admin/helpline/${id}`;

                document.getElementById('editHelplineName').value = name;
                document.getElementById('editHelplineUrl').value = url;
            });

            // Delete Modal
            const deleteModal = document.getElementById('deleteHelpLineModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                const form = document.getElementById('deleteHelpLineForm');
                form.action = `/admin/helpline/${id}`;
                document.getElementById('deleteHelpLineName').textContent = name;
            });
        });
    </script>
@endpush
