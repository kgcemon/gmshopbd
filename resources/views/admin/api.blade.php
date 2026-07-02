@extends('admin.layouts.app')

@section('content')
    <div class="p-2">

    <div class="container mt-4">
        <h4 class="fw-bold mb-3">API Settings</h4>

        <!-- Add Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add New API</button>

        <!-- Table Responsive -->
        <div id="apiTable" class="table-responsive">
            <table class="table table-bordered text-center align-middle table-hover">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Key</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="apiBody">
                @foreach($apis as $api)
                    <tr id="apiRow{{ $api->id }}">
                        <td>{{ $api->id }}</td>
                        <td>{{ $api->type }}</td>
                        <td>{{ $api->name }}</td>
                        <td>{{ $api->url }}</td>
                        <td>{{ $api->key }}</td>
                        <td>
                            @if($api->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-warning btn-sm editBtn" data-id="{{ $api->id }}" data-bs-toggle="modal" data-bs-target="#editModal{{ $api->id }}">Edit</button>
                            <button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $api->id }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- Custom Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="apiToast" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toastMessage"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="addApiForm" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add API</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Type</label>
                    <input type="text" name="type" class="form-control mb-3" required>

                    <label>Name</label>
                    <input type="text" name="name" class="form-control mb-3" required>

                    <label>URL</label>
                    <input type="text" name="url" class="form-control mb-3" required>

                    <label>Key</label>
                    <input type="text" name="key" class="form-control mb-3" required>

                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Add API</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach($apis as $api)
        <div class="modal fade" id="editModal{{ $api->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content editApiForm" data-id="{{ $api->id }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit API</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label>Type</label>
                        <input type="text" name="type" class="form-control mb-3" value="{{ $api->type }}" required>

                        <label>Name</label>
                        <input type="text" name="name" class="form-control mb-3" value="{{ $api->name }}" required>

                        <label>URL</label>
                        <input type="text" name="url" class="form-control mb-3" value="{{ $api->url }}" required>

                        <label>Key</label>
                        <input type="text" name="key" class="form-control mb-3" value="{{ $api->key }}" required>

                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ $api->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$api->status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Update API</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toastEl = document.getElementById('apiToast');
            const toast = new bootstrap.Toast(toastEl);

            function showToast(message, success=true){
                toastEl.className = 'toast align-items-center border-0 shadow-lg ' + (success ? 'bg-success text-white' : 'bg-danger text-white');
                document.getElementById('toastMessage').innerText = message;
                toast.show();
            }

            // ADD API
            document.getElementById('addApiForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');

                fetch("{{ route('admin.apis.store') }}", { method: "POST", body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            showToast(data.success);
                            this.reset();
                            location.reload();
                        } else {
                            showToast(data.error, false);
                        }
                    }).catch(() => showToast('Something went wrong!', false));
            });

            // EDIT API
            document.querySelectorAll('.editApiForm').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch(`/admin/apis/${id}`, { method: "POST", body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                showToast(data.success);
                                location.reload();
                            } else {
                                showToast(data.error, false);
                            }
                        });
                });
            });

            // DELETE API
            document.querySelectorAll('.deleteBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if(!confirm('Are you sure?')) return;
                    const id = this.dataset.id;

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'DELETE'); // 👈 Important

                    fetch(`/admin/apis/${id}`, {
                        method: "POST", // 👈 POST use korben
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                showToast(data.success);
                                document.getElementById('apiRow'+id).remove();
                            } else {
                                showToast(data.error, false);
                            }
                        });
                });
            });
        });
    </script>
@endsection
