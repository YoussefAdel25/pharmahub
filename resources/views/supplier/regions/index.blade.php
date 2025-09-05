@extends('layouts.app')

@section('content')
<div class="container py-5">
        <div id="alertContainer" class="mb-3"></div>

    <h2 class="mb-4 text-primary">Manage Your Delivery Regions</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="updateRegionsForm" action="{{ route('regions.updateRegions') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="regions" class="form-label fw-bold">Select Delivery Regions</label>
                    <select name="regions[]" id="regions" class="form-select" multiple>
                        @foreach(App\Models\Region\Region::all() as $region)
                            <option value="{{ $region->id }}"
                                {{ in_array($region->id, $supplierRegions->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Click to select/deselect regions</small>
                </div>

                <button class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Save Regions
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Your Current Delivery Regions</h5>
        </div>
        <div class="card-body" id="supplierRegionsContainer">
            @if($supplierRegions->isEmpty())
                <p class="text-muted">You havent assigned any delivery regions yet.</p>
            @else
                <div class="d-flex flex-wrap gap-2">
                    @foreach($supplierRegions as $region)
                        <span class="badge bg-secondary fs-6" id="region-{{ $region->id }}">
                            {{ $region->name }}
                            <button class="btn btn-sm btn-danger ms-1 delete-region-btn" data-id="{{ $region->id }}" type="button">&times;</button>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRegionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this region?</p>
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
    let regionToDelete = null;

    // AJAX Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Show dynamic alert
    function showAlert(message, type = 'success') {
        let alertId = 'alert-' + Date.now();
        let alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').append(alertHtml);
        setTimeout(() => { $('#' + alertId).alert('close'); }, 3000);
    }

    // Open delete modal
    $(document).on('click', '.delete-region-btn', function() {
        regionToDelete = $(this).data('id');
        $('#deleteRegionModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteBtn').click(function() {
        if(!regionToDelete) return;

        $.ajax({
            url: '/regions/destroySupplierRegion/' + regionToDelete,
            type: 'DELETE',
            success: function() {
                $('#region-' + regionToDelete).remove();
                $('#deleteRegionModal').modal('hide');
                showAlert('Region removed successfully!');
            },
            error: function(xhr){
                alert(xhr.responseJSON?.message || 'Error removing region.');
            }
        });
    });

    // Save updated regions without page reload
    $('#updateRegionsForm').submit(function(e){
        e.preventDefault();

        let form = $(this)[0]; // form DOM element
        let formData = new FormData(form); // includes CSRF token & _method

        $.ajax({
            url: '{{ route("regions.updateRegions") }}',
            type: 'POST', // use POST + _method=PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                showAlert('Regions saved successfully!');

                // Update badges dynamically
                let selectedRegions = $('#regions').val() || [];
                let container = $('#supplierRegionsContainer');
                container.empty();
                if(selectedRegions.length === 0){
                    container.html('<p class="text-muted">You haven\'t assigned any delivery regions yet.</p>');
                } else {
                    let html = '';
                    selectedRegions.forEach(id => {
                        let name = $('#regions option[value="'+id+'"]').text();
                        html += `<span class="badge bg-secondary fs-6" id="region-${id}">
                                    ${name}
                                    <button class="btn btn-sm btn-danger ms-1 delete-region-btn" data-id="${id}" type="button">&times;</button>
                                </span>`;
                    });
                    container.html(html);
                }
            },
            error: function(xhr){
                console.log(xhr.responseText); // debug
                showAlert('Error saving regions.', 'danger');
            }
        });
    });
});
</script>

@endsection
