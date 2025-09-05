@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Users</h1>

        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Create User</a>
        <button id="bulkDeleteBtn" class="btn btn-danger mb-3" style="display:none;">Delete Selected</button>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <table class="table table-bordered" id="usersTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr id="userRow{{ $user->id }}">
                        <td><input type="checkbox" class="userCheckbox" data-user-id="{{ $user->id }}"></td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger deleteBtn"
                                data-user-id="{{ $user->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the selected user(s)?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let userIdToDelete = [];
            const deleteModalEl = document.getElementById('deleteModal');
            const deleteModal = new bootstrap.Modal(deleteModalEl);
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const alertDiv = document.getElementById('alertSuccess');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectAll = document.getElementById('selectAll');

            // ===== Single Delete =====
            document.querySelectorAll('.deleteBtn').forEach(button => {
                button.addEventListener('click', function() {
                    userIdToDelete = [this.getAttribute('data-user-id')];
                    deleteModal.show();
                });
            });

            // ===== Checkbox selection & Bulk Delete button =====
            document.addEventListener('change', function(e) {
                const checkboxes = document.querySelectorAll('.userCheckbox');
                if (e.target.classList.contains('userCheckbox') || e.target.id === 'selectAll') {
                    if (e.target.id === 'selectAll') {
                        checkboxes.forEach(cb => cb.checked = e.target.checked);
                    }
                    const anyChecked = document.querySelectorAll('.userCheckbox:checked').length > 0;
                    bulkDeleteBtn.style.display = anyChecked ? 'inline-block' : 'none';
                }
            });

            // ===== Bulk Delete button click =====
            bulkDeleteBtn.addEventListener('click', function() {
                userIdToDelete = Array.from(document.querySelectorAll('.userCheckbox:checked'))
                    .map(cb => cb.getAttribute('data-user-id'));
                deleteModal.show();
            });

            // ===== Confirm Delete (single or bulk) =====
            confirmBtn.addEventListener('click', function() {
                if (!userIdToDelete || userIdToDelete.length === 0) return;

                fetch('{{ route('users.bulkDelete') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_ids: userIdToDelete
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            userIdToDelete.forEach(id => {
                                const row = document.getElementById(`userRow${id}`);
                                if (row) row.remove();
                            });
                            alertDiv.innerHTML =
                                `<div class="alert alert-success">Selected user(s) deleted successfully.</div>`;
                            deleteModal.hide();
                            userIdToDelete = [];
                            bulkDeleteBtn.style.display = 'none';
                            selectAll.checked = false;
                        }
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>
@endsection
