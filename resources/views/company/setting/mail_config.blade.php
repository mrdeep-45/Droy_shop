<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .custom-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .custom-card:hover {
            box-shadow: 0 8px 30px 0 rgba(0, 0, 0, 0.08) !important;
        }
        .form-label {
            font-size: 0.825rem !important;
            font-weight: 600 !important;
            color: #495057 !important;
            margin-bottom: 0.4rem !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control {
            border-radius: 6px !important;
            border: 1px solid #ced4da !important;
            padding: 0.6rem 0.9rem !important;
            font-size: 0.9rem !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }
        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        .input-group .form-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .btn-save {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            color: white !important;
            padding: 0.6rem 1.2rem !important;
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
            transition: all 0.2s ease-in-out !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-save:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }
        .btn-save:active {
            transform: translateY(0);
        }
        .btn-copy {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            border: none !important;
            color: white !important;
            padding: 0.6rem 1.2rem !important;
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
            transition: all 0.2s ease-in-out !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-copy:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2) !important;
        }
        .btn-copy:active {
            transform: translateY(0);
        }
        .btn-toggle-view {
            border: 1px solid #ced4da !important;
            border-left: none !important;
            border-right: none !important;
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            padding: 0.6rem 0.9rem !important;
            transition: all 0.2s !important;
            border-radius: 0 !important;
        }
        .btn-toggle-view:hover {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }
        .input-group-password .btn-save {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
    </style>
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between d-none"></div>
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card overflow-hidden shadow-sm border-0 mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-primary-transparent text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1);">
                                        <i class="fas fa-envelope-open-text fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0 fw-semibold text-dark">Mail Configuration</h5>
                                        <p class="text-muted mb-0 fs-12">Configure SMTP settings for sending system emails</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-4 pb-4 pt-2">
                                <form id="mailConfigForm" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <input type="hidden" id="company_id" name="company_id"
                                        value="{{ Session::get('company_id') }}">
                                    <div class="row">
                                        <!-- Mailer -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_MAILER" class="form-label">Mailer</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="MAIL_MAILER"
                                                        name="MAIL_MAILER"
                                                        value="{{ $data->MAIL_MAILER ?? '' }}" placeholder="smtp">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Host -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_HOST" class="form-label">Host</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="MAIL_HOST"
                                                        name="MAIL_HOST"
                                                        value="{{ $data->MAIL_HOST ?? '' }}" placeholder="smtp.mailtrap.io">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Port -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_PORT" class="form-label">Port</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="MAIL_PORT"
                                                        name="MAIL_PORT"
                                                        value="{{ $data->MAIL_PORT ?? '' }}" placeholder="2525">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Username -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_USERNAME" class="form-label">Username</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        id="MAIL_USERNAME" name="MAIL_USERNAME"
                                                        value="{{ $data->MAIL_USERNAME ?? '' }}" placeholder="Enter username">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Password -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_PASSWORD" class="form-label">Password</label>
                                                <div class="input-group input-group-password">
                                                    <input type="password" class="form-control"
                                                        id="MAIL_PASSWORD" name="MAIL_PASSWORD"
                                                        value="{{ $data->MAIL_PASSWORD ?? '' }}" placeholder="Enter password">
                                                    <button type="button" class="btn btn-toggle-view" id="togglePassword">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-save model-submit" type="button">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Encryption -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_ENCRYPTION" class="form-label">Encryption</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION"
                                                        value="{{ $data->MAIL_ENCRYPTION ?? '' }}" placeholder="tls or ssl">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- From Address -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_FROM_ADDRESS" class="form-label">From Address</label>
                                                <div class="input-group">
                                                    <input type="email" class="form-control"
                                                        id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS"
                                                        value="{{ $data->MAIL_FROM_ADDRESS ?? '' }}" placeholder="sender@example.com">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- From Name -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="MAIL_FROM_NAME" class="form-label">From Name</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        id="MAIL_FROM_NAME" name="MAIL_FROM_NAME"
                                                        value="{{ $data->MAIL_FROM_NAME ?? '' }}" placeholder="App Name">
                                                    <button class="btn btn-save model-submit" type="button"> <i
                                                            class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card custom-card overflow-hidden shadow-sm border-0 mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-info-transparent text-info rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(6, 182, 212, 0.1);">
                                        <i class="fas fa-rss fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0 fw-semibold text-dark">Pusher Configuration</h5>
                                        <p class="text-muted mb-0 fs-12">Set up Pusher credentials for real-time notifications</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-4 pb-4 pt-2">
                                <form id="pusherConfig" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <div class="row">
                                        <!-- Key -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="PUSHER_KEY" class="form-label">Key</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="PUSHER_KEY"
                                                        name="PUSHER_KEY"
                                                        value="{{ $pusher->PUSHER_KEY ?? '' }}" placeholder="Enter Pusher Key">
                                                    <button class="btn btn-save pusher-model-submit" type="button">
                                                        <i class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Secret -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="PUSHER_SECRET" class="form-label">Secret</label>
                                                <div class="input-group input-group-password">
                                                    <input type="password" class="form-control"
                                                        id="PUSHER_SECRET" name="PUSHER_SECRET"
                                                        value="{{ $pusher->PUSHER_SECRET ?? '' }}" placeholder="Enter Pusher Secret">
                                                    <button type="button" class="btn btn-toggle-view" id="toggleSecret">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-save pusher-model-submit" type="button">
                                                        <i class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- App ID -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="PUSHER_APP_ID" class="form-label">App ID</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        id="PUSHER_APP_ID" name="PUSHER_APP_ID"
                                                        value="{{ $pusher->PUSHER_APP_ID ?? '' }}" placeholder="Enter App ID">
                                                    <button class="btn btn-save pusher-model-submit" type="button">
                                                        <i class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Cluster -->
                                        <div class="col-md-6 mb-3">
                                            <div class="mb-1">
                                                <label for="PUSHER_CLUSTER" class="form-label">Cluster</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        id="PUSHER_CLUSTER" name="PUSHER_CLUSTER"
                                                        value="{{ $pusher->PUSHER_CLUSTER ?? '' }}" placeholder="e.g. ap2">
                                                    <button class="btn btn-save pusher-model-submit" type="button">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')
    <script src="{{ $actual_url . '/admin_assets/libs/flatpickr/flatpickr.min.js'}}"></script>

    <script>
        $(document).ready(function () {

            $(document).on("click", "#togglePassword", function () {
                const passwordInput = $("#MAIL_PASSWORD");
                const icon = $(this).find("i");

                const type = passwordInput.attr("type") === "password" ? "text" : "password";
                passwordInput.attr("type", type);

                if (type === "password") {
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                } else {
                    icon.removeClass("fa-eye").addClass("fa-eye-slash");
                }
            });

            $(document).on("click", "#toggleSecret", function () {
                const passwordInput = $("#PUSHER_SECRET");
                const icon = $(this).find("i");

                const type = passwordInput.attr("type") === "password" ? "text" : "password";
                passwordInput.attr("type", type);

                if (type === "password") {
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                } else {
                    icon.removeClass("fa-eye").addClass("fa-eye-slash");
                }
            });

            $(document).on("click", ".model-submit", function (e) {
                e.preventDefault();

                let $btn = $(this);
                let $wrapper = $(this).closest(".input-group");
                let $input = $wrapper.find("input, select");
                let inputVal = $input.val();
                let fieldName = $input.attr("name");


                console.log({
                    field: fieldName,
                    value: inputVal,

                });

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    }
                });

                $.ajax({
                    url: "{{ route('mail.config.update') }}",
                    type: "POST",
                    data: {
                        [fieldName]: inputVal,

                    },
                    success: function (response) {
                        $input.prop("disabled", true);
                        if (response.success) {
                            iziToast.success({
                                title: "Success",
                                message: response.message,
                                position: "topRight",
                                transitionIn: "bounceInLeft",
                                transitionOut: "fadeOutRight",
                                timeout: 1000
                            });
                            table.ajax.reload();
                            $btn.remove();
                        } else {
                            iziToast.warning({
                                title: "Warning",
                                message: response.message,
                                position: "topRight",
                                transitionIn: "bounceInLeft"
                            });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({
                            title: "Error",
                            message: xhr.responseJSON?.message || "Something went wrong!",
                            position: "topRight",
                            transitionOut: "fadeOutRight",
                            timeout: 5000
                        });
                    }
                });
            });


            $(document).on("click", ".pusher-model-submit", function (e) {
                e.preventDefault();

                let $btn = $(this);
                let $wrapper = $(this).closest(".input-group");
                let $input = $wrapper.find("input, select");
                let inputVal = $input.val();
                let fieldName = $input.attr("name");


                console.log({
                    field: fieldName,
                    value: inputVal,

                });

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    }
                });

                $.ajax({
                    url: "{{ route('pusher.config.update') }}",
                    type: "POST",
                    data: {
                        [fieldName]: inputVal,

                    },
                    success: function (response) {
                        $input.prop("disabled", true);
                        if (response.success) {
                            iziToast.success({
                                title: "Success",
                                message: response.message,
                                position: "topRight",
                                transitionIn: "bounceInLeft",
                                transitionOut: "fadeOutRight",
                                timeout: 1000
                            });
                            table.ajax.reload();
                            $btn.remove();
                        } else {
                            iziToast.warning({
                                title: "Warning",
                                message: response.message,
                                position: "topRight",
                                transitionIn: "bounceInLeft"
                            });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({
                            title: "Error",
                            message: xhr.responseJSON?.message || "Something went wrong!",
                            position: "topRight",
                            transitionOut: "fadeOutRight",
                            timeout: 5000
                        });
                    }
                });
            });

        })
    </script>

</body>

</html>