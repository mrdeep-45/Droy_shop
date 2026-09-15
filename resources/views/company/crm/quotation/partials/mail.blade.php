<div class="modal fade" id="quotationMailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="quotationMailForm">
                <div class="modal-header">
                    <h5 class="modal-title">Send Quotation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="quotation_id" name="quotation_id">

                    <div class="mb-3">
                        <label class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="quotation_email"
                               name="email"
                               placeholder="example@mail.com, demo@mail.com">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea
                            class="form-control"
                            id="quotation_message"
                            name="message"
                            rows="5"
                            placeholder="Enter your message"
                            required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.quot-mail', function () {
        $('#quotation_id').val($(this).data('id'));
        $('#quotation_email').val($(this).data('email'));
        $('#quotation_message').val($(this).data('message') || '');

        $('#quotation_email').removeClass('is-invalid');
        $('#quotation_email').next('.invalid-feedback').text('');

        $('#quotationMailModal').modal('show');
    });

    $('#quotationMailForm').on('submit', function (e) {
        e.preventDefault();

        let $btn = $(this).find('button[type="submit"]');
        let emails = $('#quotation_email').val().trim();
        let message = $('#quotation_message').val().trim();

        $('#quotation_email').removeClass('is-invalid');
        $('#quotation_email').next('.invalid-feedback').text('');
        $('#quotation_message').removeClass('is-invalid');
        $('#quotation_message').next('.invalid-feedback').text('');

        if (emails === '') {
            $('#quotation_email')
                .addClass('is-invalid')
                .next('.invalid-feedback')
                .text('Email is required.');
            return;
        }

        if (message === '') {
            $('#quotation_message')
                .addClass('is-invalid')
                .next('.invalid-feedback')
                .text('Message is required.');
            return;
        }

        let emailList = emails.split(',')
            .map(email => email.trim())
            .filter(email => email !== '');

        let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        let invalidEmails = emailList.filter(email => !regex.test(email));

        if (invalidEmails.length) {
            $('#quotation_email')
                .addClass('is-invalid')
                .next('.invalid-feedback')
                .text('Invalid email(s): ' + invalidEmails.join(', '));
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: "{{ route('quotation.send.email') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                quotation_id: $('#quotation_id').val(),
                emails: emailList,
                message: message
            },
            success: function (response) {
                iziToast.success({
                    title: 'Success',
                    message: response.message,
                    position: 'topRight'
                });

                $('#quotationMailModal').modal('hide');
                $('#quotationMailForm')[0].reset();
            },
            error: function (xhr) {
                let message = 'Something went wrong. Please try again.';

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    if (errors.emails) {
                        message = errors.emails[0];
                    } else if (errors['emails.0']) {
                        message = errors['emails.0'][0];
                    } else if (errors.quotation_id) {
                        message = errors.quotation_id[0];
                    } else {
                        message = Object.values(errors).flat()[0];
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                iziToast.error({
                    title: 'Error',
                    message: message,
                    position: 'topRight'
                });
            },
            complete: function () {
                $btn.prop('disabled', false).html('Submit');
            }
        });
    });
</script>
