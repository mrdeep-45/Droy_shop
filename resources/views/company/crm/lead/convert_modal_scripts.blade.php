$(document).ready(function () {
    // Convert to Deal Logic
    $('#modal_closing_date').flatpickr({
        dateFormat: "d-m-Y",
        minDate: "today",
        defaultDate: "today",
        static: true
    });

    $('#convert_dispatch_date').flatpickr({
        dateFormat: "d-m-Y",
        static: true
    });

    $('#modal_deal_type, #modal_deal_stage, #modal_assigned_to, #convert_deal_products, #convert_industry_id, #convert_lead_type, #convert_state_id_display, #convert_country_id_display').filter('select').select2({
        dropdownParent: $('#convertDealModal .modal-content'),
        width: '100%'
    });

    $('#convert_city_id').select2({
        dropdownParent: $('#convertDealModal .modal-content'),
        width: '100%',
        placeholder: '-- Select or Type City --',
        createTag: function (params) {
            var term = $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        ajax: {
            url: "{{ route('customer.city.search') }}",
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                return {
                    search: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.results,
                    pagination: data.pagination
                };
            }
        }
    });

    $('#convert_city_id').on('change', function () {
        if (window.isLoadingConvertData) return;
        const cityId = $(this).val();

        if (!cityId) {
            $('#convert_state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
            $('#convert_country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
            $('#convert_state_id, #convert_country_id').val('');
            return;
        }

        $.get("{{ route('customer.city.details', ':id') }}".replace(':id', cityId), function (res) {
            if (res) {
                $('#convert_state_id_display')
                    .html(`<option value="${res.state_id}" selected>${res.state_name}</option>`)
                    .trigger('change');

                $('#convert_country_id_display')
                    .html(`<option value="${res.c_id}" selected>${res.country_name}</option>`)
                    .trigger('change');

                $('#convert_state_id').val(res.state_id);
                $('#convert_country_id').val(res.c_id);

                // Auto-set Lead Type based on country
                const indiaCountryId = 99;
                const detectedType = (parseInt(res.c_id) === indiaCountryId) ? 'Domestic' : 'Export';
                $('#convert_lead_type').val(detectedType).trigger('change');
            }
        });
    });

    $('#convert_customer_id').select2({
        dropdownParent: $('#convertDealModal .modal-content'),
        width: '100%',
        placeholder: 'Select Customer',
        allowClear: true,
        ajax: {
            url: "{{ route('customer.search') }}",
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                return {
                    search: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            }
        }
    });

    // Auto-select Deal Type and Lead Type when customer selection changes
    $(document).on('change', '#convert_customer_id', function () {
        if (window.isLoadingConvertData) return;
        let customerVal = $(this).val();
        let dealType = customerVal ? 'existing' : 'new';
        $('#modal_deal_type').val(dealType).trigger('change');

        if (customerVal) {
            let url = "{{ route('customer.details', ':id') }}".replace(':id', customerVal);
            $.get(url, function (res) {
                if (res && res.customer) {
                    $('#convert_company_name').val(res.customer.company_name || '');
                    $('#convert_contact_person').val(res.customer.contact_person || '');
                    $('#convert_mobile_no').val(res.customer.phone || '');
                    $('#convert_email_address').val(res.customer.email || '');
                    $('#convert_gst_no').val(res.customer.gst_no || '');
                    $('#convert_address').val(res.customer.address || '');

                    // Populate City
                    if (res.city) {
                        var cityOpt = new Option(res.city.city_name, res.city.city_id, true, true);
                        $('#convert_city_id').empty().append(cityOpt).trigger('change');
                    } else {
                        $('#convert_city_id').val(null).trigger('change');
                    }

                    // Populate State & Country
                    if (res.state) {
                        $('#convert_state_id_display').html(`<option value="${res.state.state_id}" selected>${res.state.state_name}</option>`).trigger('change');
                        $('#convert_state_id').val(res.state.state_id);
                    } else {
                        $('#convert_state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
                        $('#convert_state_id').val('');
                    }

                    if (res.country) {
                        $('#convert_country_id_display').html(`<option value="${res.country.c_id}" selected>${res.country.country_name}</option>`).trigger('change');
                        $('#convert_country_id').val(res.country.c_id);
                    } else {
                        $('#convert_country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
                        $('#convert_country_id').val('');
                    }

                    let countryId = res.customer.country_id;
                    let autoLeadType = (countryId == 99) ? 'Domestic' : 'Export';
                    $('#convert_lead_type').val(autoLeadType).trigger('change');
                }
            });
        } else {
            // Clear fields when customer is cleared
            $('#convert_company_name').val('');
            $('#convert_contact_person').val('');
            $('#convert_mobile_no').val('');
            $('#convert_email_address').val('');
            $('#convert_gst_no').val('');
            $('#convert_address').val('');
            $('#convert_city_id').val(null).trigger('change');
            $('#convert_state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
            $('#convert_state_id').val('');
            $('#convert_country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
            $('#convert_country_id').val('');
            $('#convert_lead_type').val('').trigger('change');
        }
    });

    // Dynamic staff filtering based on selected Lead Type in convert modal
    $('#convert_lead_type').on('change', function () {
        if (window.isLoadingConvertData) return;
        let type = $(this).val(); // 'Domestic' or 'Export'
        let $select = $('#modal_assigned_to');
        let prev = $select.val();
        $.get("{{ route('enquiry.staff.by.type') }}", { type: type }, function (res) {
            if (res.status) {
                $select.empty().append('<option value="">Select Staff</option>');
                $.each(res.staff, function (i, s) {
                    $select.append('<option value="' + s.id + '" data-type="' + (s.type || '') + '">' + s.name + '</option>');
                });
                if (prev) {
                    $select.val(prev).trigger('change');
                } else {
                    $select.trigger('change');
                }
            }
        });
    });

    function updateConvertDealAmount() {
        let total = 0;
        $('#convert_deal_products option:selected').each(function () {
            let price = parseFloat($(this).data('price') || $(this).attr('data-price')) || 0;
            total += price;
        });
        $('[name="deal_amount"]', '#convertDealForm').val(total > 0 ? total.toFixed(2) : '');
    }

    // Dynamically compute deal name and deal amount from product names and prices on selection change
    $(document).on('change', '#convert_deal_products', function () {
        let selectedNames = [];
        $(this).find('option:selected').each(function () {
            selectedNames.push($(this).data('name'));
        });
        if (selectedNames.length > 0) {
            $('#convert_deal_name').val(selectedNames.join(', '));
        }
        updateConvertDealAmount();
    });

    $(document).on('click', '.convertDealRecord, .btn-convert-lead', function (e) {
        e.preventDefault();
        $('.tooltip').remove();
        let leadId = $(this).data('id');
        let customerId = $(this).data('customer') || $(this).data('customer-id');
        let assignedTo = $(this).data('assigned-to') || $('#assigned_to').val();

        $.get(`/enquiry/${leadId}/edit`, function (res) {
            if (res.status) {
                const enquiry = res.data;
                const products = res.products || [];

                // Enable loading convert data flag
                window.isLoadingConvertData = true;

                $('#convertDealForm')[0].reset();
                $('#convert_lead_id').val(leadId);
                $('#convert_deal_name').val(enquiry.enquiry_name);
                $('#convert_iwo_no').val('');

                // Reset Flatpickr selection (empty Closing Date & Dispatch Date)
                const fp = document.querySelector("#modal_closing_date")._flatpickr;
                if (fp) {
                    fp.clear();
                }
                const fpDispatch = document.querySelector("#convert_dispatch_date")._flatpickr;
                if (fpDispatch) {
                    fpDispatch.clear();
                }

                // Populate fields
                $('#convert_company_name').val(enquiry.company_name || (enquiry.customer ? enquiry.customer.company_name : ''));
                $('#convert_contact_person').val(enquiry.contact_person_name || (enquiry.customer ? enquiry.customer.contact_person : ''));
                $('#convert_mobile_no').val(enquiry.phone || (enquiry.customer ? enquiry.customer.phone : ''));
                $('#convert_email_address').val(enquiry.email || (enquiry.customer ? enquiry.customer.email : ''));
                $('#convert_gst_no').val(enquiry.customer ? enquiry.customer.gst_no : '');
                $('#convert_address').val(enquiry.customer ? enquiry.customer.address : '');
                $('#convert_industry_id').val(enquiry.industry_id || '').trigger('change');

                // Populate customer select
                if (enquiry.customer) {
                    var cOpt = new Option(enquiry.customer.company_name, enquiry.customer.customer_id, true, true);
                    $('#convert_customer_id').empty().append(cOpt).trigger('change');
                } else {
                    $('#convert_customer_id').val(null).trigger('change');
                }

                // Map the latest product IDs
                let currentProductIds = products.map(p => p.product_id);
                if (currentProductIds && currentProductIds.length > 0) {
                    $('#convert_deal_products').val(currentProductIds).trigger('change');
                } else {
                    $('#convert_deal_products').val([]).trigger('change');
                }

                // Calculate item total from lead products if available
                let leadItemTotal = 0;
                if (products && products.length > 0) {
                    products.forEach(p => {
                        let pPrice = parseFloat(p.price) || 0;
                        let pQty = parseFloat(p.quantity) || 1;
                        leadItemTotal += (pPrice * pQty);
                    });
                }

                if (leadItemTotal > 0) {
                    $('[name="deal_amount"]', '#convertDealForm').val(leadItemTotal.toFixed(2));
                } else {
                    updateConvertDealAmount();
                }

                // Auto-select Deal Type based on existing customer presence
                let dealType = enquiry.customer ? 'existing' : 'new';
                $('#modal_deal_type').val(dealType).trigger('change');

                $('#modal_deal_stage').val('').trigger('change');

                // Populate City
                if (enquiry.city) {
                    var cityOpt = new Option(enquiry.city.city_name, enquiry.city.city_id, true, true);
                    $('#convert_city_id').empty().append(cityOpt).trigger('change');
                } else {
                    $('#convert_city_id').val(null).trigger('change');
                }

                // Populate State
                if (enquiry.state) {
                    $('#convert_state_id_display').html(`<option value="${enquiry.state.state_id}" selected>${enquiry.state.state_name}</option>`).trigger('change');
                    $('#convert_state_id').val(enquiry.state.state_id);
                } else {
                    $('#convert_state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
                    $('#convert_state_id').val('');
                }

                // Populate Country
                if (enquiry.country) {
                    $('#convert_country_id_display').html(`<option value="${enquiry.country.c_id}" selected>${enquiry.country.country_name}</option>`).trigger('change');
                    $('#convert_country_id').val(enquiry.country.c_id);
                } else {
                    $('#convert_country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
                    $('#convert_country_id').val('');
                }

                // Set Lead Type and trigger change (which loads and selects the correct Assigned Staff)
                $('#convert_lead_type').val(enquiry.lead_type || '').trigger('change');

                // We must wait a tiny bit for the AJAX staff fetch to complete before setting the assigned staff value
                setTimeout(function () {
                    $('#modal_assigned_to').val(assignedTo || '').trigger('change');
                    
                    // Reset loading flag
                    window.isLoadingConvertData = false;
                }, 300);

                $('#convertDealModal').modal('show');
            } else {
                iziToast.error({ title: 'Error', message: res.message || 'Failed to fetch lead details.', position: 'topRight' });
            }
        }).fail(function () {
            iziToast.error({ title: 'Error', message: 'Failed to fetch lead details.', position: 'topRight' });
        });
    });

    // Prevent Background Scrolling when Modal is Open
    $(document).on('show.bs.modal', '#convertDealModal', function () {
        $('body, html, .main-content, .app-content').css({
            'overflow': 'hidden',
            'height': '100vh'
        });
    });

    $(document).on('hidden.bs.modal', '#convertDealModal', function () {
        $('body, html, .main-content, .app-content').css({
            'overflow': '',
            'height': ''
        });
    });

    $('#convertDealForm').on('submit', function (e) {
        e.preventDefault();

        // Client-side Validation
        let isValid = true;
        let productIds = $('#convert_deal_products').val();
        let dealAmount = $('[name="deal_amount"]', '#convertDealForm').val().trim();
        let closingDate = $('[name="closing_date"]', '#convertDealForm').val().trim();
        let dealStage = $('[name="deal_stage"]', '#convertDealForm').val();
        let leadType = $('[name="lead_type"]', '#convertDealForm').val();
        let assignedTo = $('[name="assigned_to"]', '#convertDealForm').val();
        let contactPerson = $('[name="contact_person"]', '#convertDealForm').val().trim();
        let mobileNo = $('[name="mobile_no"]', '#convertDealForm').val().trim();
        let emailAddress = $('[name="email_address"]', '#convertDealForm').val().trim();
        let companyName = $('[name="company_name"]', '#convertDealForm').val().trim();

        if (!companyName) {
            iziToast.warning({ title: 'Warning', message: 'Company Name is required.', position: 'topRight' });
            isValid = false;
        }

        if (!productIds || productIds.length === 0) {
            iziToast.warning({ title: 'Warning', message: 'Please select at least one product.', position: 'topRight' });
            isValid = false;
        }
        if (!dealAmount) {
            iziToast.warning({ title: 'Warning', message: 'Deal Amount is required.', position: 'topRight' });
            isValid = false;
        } else if (isNaN(dealAmount) || parseFloat(dealAmount) <= 0) {
            iziToast.warning({ title: 'Warning', message: 'Please enter a valid Deal Amount.', position: 'topRight' });
            isValid = false;
        }
        if (!closingDate) {
            iziToast.warning({ title: 'Warning', message: 'Closing Date is required.', position: 'topRight' });
            isValid = false;
        }
        if (!contactPerson) {
            iziToast.warning({ title: 'Warning', message: 'Contact Person is required.', position: 'topRight' });
            isValid = false;
        }

        if (!emailAddress) {
            iziToast.warning({ title: 'Warning', message: 'Email Address is required.', position: 'topRight' });
            isValid = false;
        }
        if (!dealStage) {
            iziToast.warning({ title: 'Warning', message: 'Please select a Deal Stage.', position: 'topRight' });
            isValid = false;
        }
        if (!leadType) {
            iziToast.warning({ title: 'Warning', message: 'Please select a Lead Type.', position: 'topRight' });
            isValid = false;
        }
        if (!assignedTo) {
            iziToast.warning({ title: 'Warning', message: 'Please select a staff member to assign to.', position: 'topRight' });
            isValid = false;
        }

        if (!isValid) {
            return false;
        }

        const btn = $('#btnSubmitConvert');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Converting...');

        $.ajax({
            url: "{{ route('enquiry.convert') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.status) {
                    iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                    $('#convertDealModal').modal('hide');
                    if ($('#lead_table').length && $.fn.DataTable.isDataTable('#lead_table')) {
                        $('#lead_table').DataTable().ajax.reload(null, false);
                    } else {
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                }
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function (key, value) {
                        iziToast.warning({ title: 'Warning', message: value[0], position: 'topRight' });
                    });
                } else {
                    iziToast.error({ title: 'Error', message: 'Something went wrong!', position: 'topRight' });
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Convert');
            }
        });
    });
});
