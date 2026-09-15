<!-- Customer Creation Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCustomerModalLabel">Create New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCustomerForm" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" required>
                            <div class="invalid-feedback">Please enter Company Name.</div>
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="contact_person" required>
                            <div class="invalid-feedback">Please enter Contact Person.</div>
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                            <div class="invalid-feedback">Please enter valid Email.</div>
                        </div>
                        
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" maxlength="15" pattern="[0-9()+\- ]*" oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">GST No.</label>
                            <input type="text" class="form-control" name="gst_no">
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">PAN No.</label>
                            <input type="text" class="form-control" name="pan_no">
                        </div>
                        
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">City</label>
                            <select class="form-select" name="city_id" id="modal_city_id"></select>
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">State</label>
                            <select class="form-select" name="state_id" id="modal_state_id" readonly style="pointer-events: none; background-color: #e9ecef;"></select>
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <label class="form-label">Country</label>
                            <select class="form-select" name="country_id" id="modal_country_id" readonly style="pointer-events: none; background-color: #e9ecef;"></select>
                        </div>
                        
                        <div class="col-12 mb-2">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveCustomer">Save Customer</button>
            </div>
        </div>
    </div>
</div>