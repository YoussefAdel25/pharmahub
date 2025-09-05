@extends('layouts.app')

@section('head')
<style>
.card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.card-header {
    font-weight: bold;
    font-size: 1.2rem;
}
.table td, .table th {
    vertical-align: middle;
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Regions</span>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRegionModal">Add Region</button>
        </div>
        <div class="card-body">
            <div id="alert-container"></div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="regionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Region Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regions as $region)
                        <tr id="region-{{ $region->id }}">
                            <td>{{ $region->id }}</td>
                            <td class="region-name">{{ $region->name }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-btn"
                                        data-id="{{ $region->id }}"
                                        data-name="{{ $region->name }}"
                                        data-bs-toggle="modal" data-bs-target="#editRegionModal">
                                    Edit
                                </button>

                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $region->id }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $regions->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Add Region Modal -->
<div class="modal fade" id="addRegionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="addRegionForm" class="modal-content">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title">Add New Region</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" id="addRegionName" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success" type="submit">Save</button>
        </div>
    </form>
  </div>
</div>

<!-- Edit Region Modal -->
<div class="modal fade" id="editRegionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="editRegionForm" class="modal-content">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title">Edit Region</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editRegionId">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" id="editRegionName" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRegionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-danger">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this region?</p>
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
    // AJAX Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Show alert
    function showAlert(message, type='success') {
        $('#alert-container').html(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
        setTimeout(() => { $('.alert').alert('close'); }, 3000);
    }

    // Add Region
    $('#addRegionForm').submit(function(e){
        e.preventDefault();
        let name = $('#addRegionName').val();
        $.post('{{ route("region.store") }}', {name})
            .done(function(data){
                $('#addRegionModal').modal('hide');
                $('#regionsTable tbody').prepend(`<tr id="region-${data.id}">
                    <td>${data.id}</td>
                    <td class="region-name">${data.name}</td>
                    <td>
                        <button class="btn btn-primary btn-sm edit-btn" data-id="${data.id}" data-name="${data.name}" data-bs-toggle="modal" data-bs-target="#editRegionModal">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${data.id}">Delete</button>
                    </td>
                </tr>`);
                $('#addRegionForm')[0].reset();
                showAlert('Region added successfully!');
            })
            .fail(function(xhr){
                alert(xhr.responseJSON.message || 'Error adding region.');
            });
    });

    // Edit Modal populate
    $(document).on('click', '.edit-btn', function(){
        let id = $(this).data('id');
        let name = $(this).data('name');
        $('#editRegionId').val(id);
        $('#editRegionName').val(name);
    });

    // Update Region
    $('#editRegionForm').submit(function(e){
        e.preventDefault();
        let id = $('#editRegionId').val();
        let name = $('#editRegionName').val();
        $.ajax({
            url: `/regions/update/${id}`,
            type: 'PUT',
            data: {name},
            success: function(data){
                $(`#region-${id} .region-name`).text(data.name);
                $(`#region-${id} .edit-btn`).data('name', data.name);
                $('#editRegionModal').modal('hide');
                showAlert('Region updated successfully!');
            },
            error: function(xhr){
                alert(xhr.responseJSON.message || 'Error updating region.');
            }
        });
    });

    // Delete Region
    let deleteRegionId = null;
    $(document).on('click', '.delete-btn', function(){
        deleteRegionId = $(this).data('id');
        $('#deleteRegionModal').modal('show');
    });

    $('#confirmDeleteBtn').click(function(){
        if(!deleteRegionId) return;
        $.ajax({
            url: `/regions/delete/${deleteRegionId}`,
            type: 'DELETE',
            success: function(){
                $(`#region-${deleteRegionId}`).remove();
                $('#deleteRegionModal').modal('hide');
                showAlert('Region deleted successfully!');
            },
            error: function(xhr){
                alert(xhr.responseJSON.message || 'Error deleting region.');
            }
        });
    });
});
</script>
@endsection
