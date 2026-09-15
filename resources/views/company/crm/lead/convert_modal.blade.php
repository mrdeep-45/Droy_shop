@php
    $modal_industries = DB::table('mst_industry_type')
        ->select('industry_type_id', 'industry_type_name')
        ->where('status', 0)
        ->orderBy('industry_type_name', 'asc')
        ->get();
    
    $modal_staffList = DB::table('mst_staff')
        ->select('staff_id', 'staff_name', 'staff_type')
        ->where('status', 0)
        ->orderBy('staff_name')
        ->get();
@endphp

<!-- Convert to Deal Modal -->
<div class="modal fade" id="convertDealModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-handshake me-1"></i> Convert Lead to Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="convertDealForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" id="convert_lead_id">
                    <input type="hidden" name="deal_name" id="convert_deal_name">
                    @if(isStaffLogin())
                        <input type="hidden" name="deal_type" id="modal_deal_type" value="new">
                        <input type="hidden" name="lead_type" id="convert_lead_type" value="{{ $lead->lead_type ?? 'Domestic' }}">
                    @endif

                    <div class="row g-3">
                        <!-- Row 1 -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Existing Customer</label>
                            <select class="form-select" name="customer_id" id="convert_customer_id"
                                data-placeholder="Select Customer" style="width: 100%;">
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="company_name" id="convert_company_name"
                                placeholder="Enter Company Name" >
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="contact_person" id="convert_contact_person"
                                placeholder="Enter Contact Person" required>
                        </div>

                        <!-- Row 2 -->
                        <div class="col-lg-4 col-md-6 col-12">
                             <label class="form-label form-label-bold">Mobile No</label>
                             <input type="text" class="form-control" name="mobile_no" id="convert_mobile_no"
                                 placeholder="Enter Mobile No">
                         </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email_address" id="convert_email_address"
                                placeholder="Enter Email Address" required>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">GST No</label>
                            <input type="text" class="form-control" name="gst_no" id="convert_gst_no"
                                placeholder="Enter GST Number ">
                        </div>

                        <!-- Address and Location Fields -->
                        <div class="col-12">
                            <label class="form-label form-label-bold">Address</label>
                            <textarea class="form-control" rows="2" name="address" id="convert_address"
                                placeholder="Enter Address"></textarea>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">City</label>
                            <select class="form-select" id="convert_city_id" name="city_id"></select>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">State</label>
                            <select class="form-select" id="convert_state_id_display">
                                <option value="">-- Select State --</option>
                            </select>
                            <input type="hidden" id="convert_state_id" name="state_id">
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Country</label>
                            <select class="form-select" id="convert_country_id_display">
                                <option value="">-- Select Country --</option>
                            </select>
                            <input type="hidden" id="convert_country_id" name="country_id">
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Industry</label>
                            <select class="form-select" name="industry_id" id="convert_industry_id"
                                data-placeholder="-- Select Industry --" style="width: 100%;">
                                <option value="">-- Select Industry --</option>
                                @foreach ($modal_industries as $ind)
                                    <option value="{{ $ind->industry_type_id }}">{{ $ind->industry_type_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">IWO No</label>
                            <input type="text" class="form-control" name="iwo_no" id="convert_iwo_no"
                                placeholder="Enter IWO No ">
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Dispatch Date</label>
                            <input type="text" class="form-control" name="dispatch_date" id="convert_dispatch_date"
                                placeholder="DD-MM-YYYY ">
                        </div>

                        <!-- Row 3 - Products select -->
                        <div class="col-12">
                            <label class="form-label form-label-bold">Products <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="product_ids[]" id="convert_deal_products"
                                multiple required data-placeholder="-- Select Product(s) --" style="width: 100%;">
                                @isset($products)
                                    @foreach ($products as $item)
                                        <option value="{{ $item->product_id }}" data-name="{{ $item->prod_name }}" data-price="{{ $item->prod_amount ?? 0 }}">
                                            {{ $item->prod_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <!-- Row 4 -->
                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Deal Amount (No GST) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="deal_amount" placeholder="0.00"
                                >
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Closing Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_closing_date" name="closing_date"
                                placeholder="DD-MM-YYYY" required>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <label class="form-label form-label-bold">Deal Stage <span class="text-danger">*</span></label>
                            <select class="form-select" name="deal_stage" id="modal_deal_stage" >
                                <option value="">Select Stage</option>
                                @isset($stages)
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <!-- Row 5 -->
                        @if(!isStaffLogin())
                            <div class="col-lg-4 col-md-6 col-12">
                                <label class="form-label form-label-bold">Deal Type</label>
                                <select class="form-select" name="deal_type" id="modal_deal_type">
                                    <option value="">Select Type</option>
                                    <option value="new">New Business</option>
                                    <option value="existing">Existing Business</option>
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 col-12">
                                 <label class="form-label form-label-bold">Lead Type <span class="text-danger">*</span></label>
                                 <select class="form-select" name="lead_type" id="convert_lead_type" required>
                                     <option value="">Select Lead Type</option>
                                     <option value="Domestic">Domestic</option>
                                     <option value="Export">Export</option>
                                 </select>
                            </div>
                        @endif

                        <div class="col-lg-4 col-md-6 col-12">
                             <label class="form-label form-label-bold">Assigned To <span class="text-danger">*</span></label>
                             <select class="form-select" name="assigned_to" id="modal_assigned_to" required>
                                 <option value="">Select Staff</option>
                                 @foreach ($modal_staffList as $staff)
                                     <option value="{{ $staff->staff_id }}" {{ (session('staff_id') == $staff->staff_id) ? 'selected' : '' }} data-type="{{ $staff->staff_type }}">{{ $staff->staff_name }}</option>
                                 @endforeach
                             </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                              @include('components.tooltip-help', ['key' => 'deal_convert_description', 'label' => 'Description', 'class' => 'form-label form-label-bold'])
                            {{-- <label class="form-label form-label-bold">Description</label> --}}
                            <textarea class="form-control" rows="3" name="description"
                                placeholder="Write deal details or summary..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitConvert">
                        <i class="fas fa-check me-1"></i> Convert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
