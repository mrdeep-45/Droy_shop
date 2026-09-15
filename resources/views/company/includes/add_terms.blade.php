<div class="modal fade" id="terms_conditions_modal_global" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="terms_conditions_form_global">
            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Create Terms & Conditions
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Module <span class="text-danger">*</span>
                        </label>

                        <input type="text" class="form-control" id="module_global" name="module[]">

                        <div class="invalid-feedback"></div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold">
                            Terms & Conditions
                            <span class="text-danger">*</span>
                        </label>

                        <textarea class="form-control" rows="6" id="terms_global" name="terms[]"></textarea>

                        <div class="invalid-feedback"></div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary" id="submit_terms_global">

                        <span id="submit_terms_text_global">
                            Submit
                        </span>

                        <span id="submit_terms_loader_global" class="spinner-border spinner-border-sm d-none">
                        </span>

                    </button>

                </div>

            </div>

        </form>
    </div>
</div>

<script>

    const TERMS_GLOBAL = {
        storeUrl: "{{ route('terms.store') }}"
    };

    function clearValidationGlobal() {
        $('#terms_conditions_form_global .is-invalid')
            .removeClass('is-invalid');

        $('#terms_conditions_form_global .invalid-feedback')
            .text('');
    }

    $(document).on("click", "#add_terms_conditions_global", function () {

        $("#terms_conditions_form_global")[0].reset();

        clearValidationGlobal();

        $("#terms_conditions_modal_global").modal("show");

    });

    $("#terms_conditions_form_global").on("submit", function (e) {

        e.preventDefault();

        clearValidationGlobal();

        let valid = true;

        if (!$("#module_global").val().trim()) {
            valid = false;

            $("#module_global")
                .addClass("is-invalid")
                .next(".invalid-feedback")
                .text("Module is required.");
        }

        if (!$("#terms_global").val().trim()) {
            valid = false;

            $("#terms_global")
                .addClass("is-invalid")
                .next(".invalid-feedback")
                .text("Terms & Conditions is required.");
        }

        if (!valid) {
            iziToast.warning({
                title: "Validation",
                message: "Please fill all required fields.",
                position: "topRight"
            });
            return;
        }

        $("#submit_terms_global").prop("disabled", true);
        $("#submit_terms_text_global").text("Submitting...");
        $("#submit_terms_loader_global").removeClass("d-none");

        $.ajax({
            url: TERMS_GLOBAL.storeUrl,
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                iziToast.success({
                    title: "Success",
                    message: response.message,
                    position: "topRight"
                });

                $("#terms_conditions_modal_global").modal("hide");

                $("#terms_conditions_form_global")[0].reset();

                if ($("#terms_select").length) {
                    $("#terms_select").val(null).trigger("change");
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        $("#" + key + "_global")
                            .addClass("is-invalid")
                            .next(".invalid-feedback")
                            .text(value[0]);
                    });
                } else {
                    iziToast.error({
                        title: "Error",
                        message: xhr.responseJSON?.message || "Something went wrong.",
                        position: "topRight"
                    });
                }
            },
            complete: function () {
                $("#submit_terms_global").prop("disabled", false);
                $("#submit_terms_text_global").text("Submit");
                $("#submit_terms_loader_global").addClass("d-none");
            }
        });

    });

    $(document).on("input", "#module_global,#terms_global", function () {
        $(this).removeClass("is-invalid");
        $(this).next(".invalid-feedback").text("");
    });
</script>
