<div class="modal fade" id="mainMailModal" tabindex="-1" aria-labelledby="mainMailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <form id="mainMailForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white fw-bold fs-15" id="mainMailModalLabel">
                        <i class="fa-solid fa-envelope me-2"></i>Send Tax Invoice via Email
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="main_mail_invoice_id" name="invoice_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-12">Recipient Email(s) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="main_performa_email" name="email"
                            placeholder="example@mail.com, demo@mail.com">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-12">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="main_performa_message" name="message" rows="5"
                            placeholder="Enter your message..." required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.invoice-mail', function () {
        const id = $(this).data('id');
        const email = $(this).data('email') || '';
        const msg = $(this).data('message') || '';

        $('#main_mail_invoice_id').val(id);
        $('#main_performa_email').val(email);
        $('#main_performa_message').val(msg);

        $('#main_performa_email, #main_performa_message').removeClass('is-invalid');
        $('#main_performa_email').next('.invalid-feedback').text('');
        $('#main_performa_message').next('.invalid-feedback').text('');

        $('#mainMailModal').modal('show');
    });

    $('#mainMailForm').on('submit', function (e) {
        e.preventDefault();

        const $btn = $(this).find('button[type="submit"]');
        const emails = $('#main_performa_email').val().trim();
        const message = $('#main_performa_message').val().trim();

        $('#main_performa_email, #main_performa_message').removeClass('is-invalid');
        $('#main_performa_email').next('.invalid-feedback').text('');
        $('#main_performa_message').next('.invalid-feedback').text('');

        if (emails === '') {
            $('#main_performa_email').addClass('is-invalid').next('.invalid-feedback').text('Email is required.');
            return;
        }

        if (message === '') {
            $('#main_performa_message').addClass('is-invalid').next('.invalid-feedback').text('Message is required.');
            return;
        }

        const emailList = emails.split(',').map(e => e.trim()).filter(e => e !== '');
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const invalidEmails = emailList.filter(e => !regex.test(e));

        if (invalidEmails.length) {
            $('#main_performa_email').addClass('is-invalid').next('.invalid-feedback').text('Invalid email(s): ' + invalidEmails.join(', '));
            return;
        }

        const origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Sending...');

        const invoiceId = $('#main_mail_invoice_id').val();
        const sendUrl = "{{ route('main_invoice.send_email', ':id') }}".replace(':id', invoiceId);

        $.ajax({
            url: sendUrl,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                emails: emailList,
                message: message
            },
            success: function (response) {
                if (response.status) {
                    iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                    $('#mainMailModal').modal('hide');
                    $('#mainMailForm')[0].reset();
                } else {
                    iziToast.error({ title: 'Error', message: response.message || 'Failed to send email.', position: 'topRight' });
                }
            },
            error: function (xhr) {
                let msg = 'Something went wrong. Please try again.';
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    msg = Object.values(errors).flat()[0];
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
            },
            complete: function () {
                $btn.prop('disabled', false).html(origHtml);
            }
        });
    });
</script>
