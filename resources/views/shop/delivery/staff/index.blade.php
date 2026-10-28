<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Delivery Staff — DPShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    {{-- Page Header --}}
    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-cycling text-shop-primary me-2"></i>{{ $page_name }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / Delivery / <span>Delivery Staff</span>
            </div>
        </div>
        <button class="btn btn-primary" id="addStaffBtn" style="border-radius:10px;font-size:0.85rem;font-weight:600;">
            <i class="bx bx-plus me-1"></i> Add Delivery Staff
        </button>
    </div>

    {{-- Main Card --}}
    <div class="shop-card">
        <div class="card-header">
            <span><i class="bx bx-list-ul me-2 text-primary"></i>All Delivery Personnel</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="staffTable" class="table table-hover w-100" style="font-size:0.83rem;">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Staff Member</th>
                            <th>Contact</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th>Active Deliveries</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

{{-- ══════ ADD / EDIT MODAL ══════ --}}
<div class="modal fade" id="staffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom:1px solid #f0f3f8;padding:20px 24px;">
                <h5 class="modal-title fw-bold" id="staffModalTitle">Add Delivery Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="staffForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="staff_id" id="staff_id">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required placeholder="e.g. John Doe">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" required placeholder="rider@dpshop.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="phone" id="phone" required placeholder="9876543210">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Password <span id="pwdHint" class="text-muted fw-normal">(Required for new)</span></label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="••••••••">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Vehicle Type</label>
                            <select class="form-select" name="vehicle_type" id="vehicle_type">
                                <option value="bike">Bike / Motorcycle</option>
                                <option value="scooter">Scooter / EV</option>
                                <option value="cycle">Bicycle</option>
                                <option value="van">Van / Small Truck</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;">Vehicle Plate #</label>
                            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" placeholder="DL 01 AB 1234">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.82rem;">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/*">
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" checked>
                        <label class="form-check-label fw-semibold" for="is_available" style="font-size:0.82rem;">Mark as Available for Deliveries</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f3f8;padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveStaffBtn">Save Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('shop.include.footer_link')

<script>
$(function() {
    const table = $('#staffTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('shop.delivery.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'profile_display', name: 'name' },
            { data: 'contact_info', name: 'email' },
            { data: 'vehicle_info', name: 'vehicle_type' },
            { data: 'status_badge', name: 'is_available' },
            { data: 'active_orders', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#addStaffBtn').on('click', function() {
        $('#staffForm')[0].reset();
        $('#staff_id').val('');
        $('#staffModalTitle').text('Add Delivery Staff');
        $('#pwdHint').text('(Required for new staff)');
        $('#password').prop('required', true);
        $('#staffModal').modal('show');
    });

    $('#staffForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $('#saveStaffBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('shop.delivery.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#saveStaffBtn').prop('disabled', false).text('Save Staff');
                if (res.status === 'success') {
                    $('#staffModal').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr) {
                $('#saveStaffBtn').prop('disabled', false).text('Save Staff');
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    $(document).on('click', '.edit-staff-btn', function() {
        const id = $(this).data('id');
        $.get("{{ url('/shop/delivery-staff/edit') }}/" + id, function(res) {
            if (res.status === 'success') {
                const s = res.data;
                $('#staff_id').val(s.delivery_staff_id);
                $('#name').val(s.name);
                $('#email').val(s.email);
                $('#phone').val(s.phone);
                $('#vehicle_type').val(s.vehicle_type);
                $('#vehicle_number').val(s.vehicle_number);
                $('#is_available').prop('checked', s.is_available == 1);
                $('#staffModalTitle').text('Edit Delivery Staff');
                $('#pwdHint').text('(Leave blank to keep current)');
                $('#password').prop('required', false);
                $('#staffModal').modal('show');
            }
        });
    });

    $(document).on('click', '.delete-staff-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Delivery Staff?',
            text: "This staff member will be removed from available drivers.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('shop.delivery.delete') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id
                }, function(res) {
                    if (res.status === 'success') {
                        table.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Deleted', text: res.message, timer: 1500, showConfirmButton: false });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>
