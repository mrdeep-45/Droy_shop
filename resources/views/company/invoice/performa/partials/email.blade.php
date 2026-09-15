<div class="modal fade" id="performaMailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="performaMailForm">
                <div class="modal-header">
                    <h5 class="modal-title">Send Performa Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="mail_invoice_id" name="invoice_id">

                    <div class="mb-3">
                        <label class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="performa_email" name="email"
                            placeholder="example@mail.com, demo@mail.com">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="performa_message" name="message" rows="5"
                            placeholder="Enter your message" required></textarea>
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

    function showToast(type, title, message) {
        iziToast[type]({
            title: title,
            message: message,
            position: 'topRight',
            timeout: 5000,
            closeOnClick: true,
            progressBar: true
        });
    }

    $(document).on('click', '.invoice-mail', function () {
        $('#mail_invoice_id').val($(this).data('id'));
        $('#performa_email').val($(this).data('email'));
        $('#performa_message').val($(this).data('message') || '');

        $('#performa_email').removeClass('is-invalid');
        $('#performa_email').next('.invalid-feedback').text('');
        $('#performa_message').removeClass('is-invalid');
        $('#performa_message').next('.invalid-feedback').text('');

        $('#performaMailModal').modal('show');
    });

    $('#performaMailForm').on('submit', function (e) {
        e.preventDefault();

        let $btn = $(this).find('button[type="submit"]');
        let emails = $('#performa_email').val().trim();
        let message = $('#performa_message').val().trim();

        $('#performa_email').removeClass('is-invalid');
        $('#performa_email').next('.invalid-feedback').text('');
        $('#performa_message').removeClass('is-invalid');
        $('#performa_message').next('.invalid-feedback').text('');

        if (emails === '') {
            $('#performa_email')
                .addClass('is-invalid')
                .next('.invalid-feedback')
                .text('Email is required.');
            return;
        }

        if (message === '') {
            $('#performa_message')
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
            $('#performa_email')
                .addClass('is-invalid')
                .next('.invalid-feedback')
                .text('Invalid email(s): ' + invalidEmails.join(', '));
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: "{{ route('performa_invoice.send_email', ':id') }}".replace(':id', $('#mail_invoice_id').val()),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                emails: emailList,
                message: message
            },
            success: function (response) {
                if (response.status) {
                    showToast('success', 'Success', response.message);
                    $('#performaMailModal').modal('hide');
                    $('#performaMailForm')[0].reset();
                } else {
                    showToast('error', 'Error', response.message || 'Failed to send email.');
                }
            },
            error: function (xhr) {
                let msg = 'Something went wrong. Please try again.';
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.emails) {
                        msg = errors.emails[0];
                    } else if (errors['emails.0']) {
                        msg = errors['emails.0'][0];
                    } else if (errors.message) {
                        msg = errors.message[0];
                    } else {
                        msg = Object.values(errors).flat()[0];
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast('error', 'Error', msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('Submit');
            }
        });
    });
</script>
