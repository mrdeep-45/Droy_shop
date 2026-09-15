<!DOCTYPE html>
<html {!! get_html_data() !!}>
<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <link rel="stylesheet" href="{{ $actual_url . '/admin-assets/css/documents.css' }}">
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
                         <div class="col card-background-mt flex-fill">
                            <div class="card custom-card border-0 shadow-sm" style="border-radius: 16px;">
                                {{-- <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="card-title text-primary mb-0" id="pageTitleLabel">
                                        {{ $page_name }}
                                    </div>
                                    <ul class="nav nav-tabs nav-tabs-footer mb-0" role="tablist">
                                        @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#campaign-list">List</a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#new-campaign" id="campaignTabLabel">New Campaign</a>
                                        </li>
                                        @endif
                                    </ul>
                                </div> --}}
                                <div class="card-body p-3 p-md-4">
                                    <div class="d-flex mb-3 align-items-center justify-content-between border-bottom pb-3">
                                            <div>
                                                <h5 class="mb-1 fw-bold text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                        <i class="fa fa-folder-open fs-6"></i>
                                                    </div>
                                                    Document Categories
                                                </h5>
                                                <p class="text-muted mb-0 fs-13 ms-5 ps-1">Manage and organize your system document directories.</p>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <button id="reloadBtn" class="btn btn-light text-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; padding: 0;" title="Refresh Directory">
                                                    <i class="fa fa-refresh fs-14"></i>
                                                </button>
                                                @if(permissions_check('canCreate'))
                                                <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill px-3 py-1 fw-semibold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                                    <i class="fa fa-plus me-1 fs-12"></i> New Category
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div id="categoriesContainer" class="row g-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--model -->
       <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);">
                            <div class="modal-header custom-modal-header pb-2 pt-3 px-3 rounded-top-4">
                                <h6 class="modal-title fw-bold d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fa fa-folder-plus fs-14"></i>
                                    </div>
                                    Create New Category
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-3 pb-3 pt-3">
                                <form id="CategorySubmit">
                                    <div id="categoryFields">
                                        <div class="row category-row mb-2 align-items-end">
                                            <div class="col-md-10 col-10">
                                                <label class="form-label fw-bold text-slate-700 fs-12 mb-1">Category Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control rounded-3 category-input fs-14" name="category_name[]" placeholder="e.g., Financial Reports" required>
                                                <div class="invalid-feedback category-error">Please enter Category</div>
                                            </div>
                                            <div class="col-md-2 col-2">
                                                <button type="button" class="btn btn-danger rounded-3 remove-row d-none w-100 shadow-sm" title="Remove" style="height: 38px;">
                                                    <i class="fa fa-trash fs-14"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3 py-1" id="addMore">
                                            <i class="fa fa-plus me-1"></i> Add Another
                                        </button>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm" id="submitBtn">
                                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
 
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')
<!-- Add this right before your custom script tags if you want to use moment -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
$(document).on('dblclick', '.folder-card', function(e) {
                    // Prevent trigger if clicking action buttons
                    if($(e.target).closest('.action-btns').length > 0) return;

                    const encryptedId = $(this).data('id');
                    window.location.href = `{{ url('/documents/category') }}/${encryptedId}`;
                });

            const themeColors = [
                { bg: 'linear-gradient(135deg, #0ea5e9, #0284c7)', shadow: 'rgba(14, 165, 233, 0.3)' },
                { bg: 'linear-gradient(135deg, #f59e0b, #d97706)', shadow: 'rgba(245, 158, 11, 0.3)' },
                { bg: 'linear-gradient(135deg, #22c55e, #16a34a)', shadow: 'rgba(34, 197, 94, 0.3)' },
                { bg: 'linear-gradient(135deg, #ef4444, #dc2626)', shadow: 'rgba(239, 68, 68, 0.3)' },
                { bg: 'linear-gradient(135deg, #a855f7, #9333ea)', shadow: 'rgba(168, 85, 247, 0.3)' },
                { bg: 'linear-gradient(135deg, #f97316, #ea580c)', shadow: 'rgba(249, 115, 22, 0.3)' },
                { bg: 'linear-gradient(135deg, #14b8a6, #0d9488)', shadow: 'rgba(20, 184, 166, 0.3)' },
                { bg: 'linear-gradient(135deg, #ec4899, #db2777)', shadow: 'rgba(236, 72, 153, 0.3)' }
            ];

            let canEditGlobal = false;
            let canDeleteGlobal = false;

            loadCategories();

            function showEmptyState() {
                $('#categoriesContainer').html(`
                    <div class="col-12 text-center py-4 empty-state">
                        <div class="mb-3"><i class="fa fa-folder-open text-slate-300" style="font-size: 48px;"></i></div>
                        <h6 class="fw-bolder text-slate-800 mb-1">No Directory Found</h6>
                        <p class="text-slate-500 mb-0 fs-13">Your workspace is empty. Start organizing by creating your first category.</p>
                    </div>
                `);
            }

            function generateCategoryCard(category, index) {
                const theme = themeColors[index % themeColors.length];
                const animDelay = (index % 10) * 0.05;

                let formattedDate = 'N/A';
                if (category.created_at) {
                    const d = new Date(category.created_at);
                    if (!isNaN(d)) {
                        formattedDate = d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    } else {
                        formattedDate = category.created_at;
                    }
                }

                return `
                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-6 category-wrapper" id="cat-card-${category.encrypted_id}" style="animation-delay: ${animDelay}s;">
                    <div class="card folder-card h-100 d-flex flex-column" data-id="${category.encrypted_id}" style="cursor: pointer; transition: transform 0.2s;">
                        <div class="card-body p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="icon-wrapper shadow-sm" style="background: ${theme.bg}; box-shadow: 0 4px 10px -2px ${theme.shadow} !important;">
                                    <i class="fa fa-folder-open"></i>
                                </div>
                                <div class="action-btns d-flex gap-1">
                                    ${canEditGlobal ? `
                                    <a class="edit-category text-decoration-none" href="javascript:void(0);" data-id="${category.encrypted_id}" title="Rename">
                                        <div class="action-icon-bg edit-btn"><i class="fa fa-pencil"></i></div>
                                    </a>` : ''}
                                    ${canDeleteGlobal ? `
                                    <a class="delete-category text-decoration-none" href="javascript:void(0);" data-id="${category.encrypted_id}" data-name="${category.d_category}" title="Delete">
                                        <div class="action-icon-bg delete-btn"><i class="fa fa-trash"></i></div>
                                    </a>` : ''}
                                </div>
                            </div>

                            <div class="category-title-container flex-grow-1">
                                <h6 class="fw-bolder mb-1 text-truncate category-name-text" title="${category.d_category}">
                                    ${category.d_category}
                                </h6>
                                <div class="category-meta d-flex align-items-center mt-1">
                                    <i class="fa fa-file-alt me-1 opacity-50 fs-10"></i> System Directory
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-top" style="border-color: rgba(226, 232, 240, 0.6) !important;">
                                <div class="d-flex justify-content-end align-items-center fs-11 text-slate-500">
                                    <div class="d-flex align-items-center flex-shrink-0" title="Date: ${formattedDate}">
                                        <i class="fa fa-clock me-1 opacity-75 fs-12"></i>
                                        <span>${formattedDate}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }

            $('#addMore').on('click', function() {
                const newRow = `
                <div class="row category-row mb-2 align-items-end" style="display:none;">
                    <div class="col-md-10 col-10">
                        <input type="text" class="form-control rounded-3 category-input fs-14" name="category_name[]" placeholder="e.g., Financial Reports" required>
                        <div class="invalid-feedback category-error">Please enter Category</div>
                    </div>
                    <div class="col-md-2 col-2">
                        <button type="button" class="btn btn-danger rounded-3 remove-row w-100 shadow-sm" style="height: 38px;">
                            <i class="fa fa-trash fs-14"></i>
                        </button>
                    </div>
                </div>`;
                const $newRow = $(newRow);
                $('#categoryFields').append($newRow);
                $newRow.slideDown(200);
                toggleRemoveButtons();
            });

            $(document).on('click', '.remove-row', function() {
                if ($('.category-row').length > 1) {
                    const $row = $(this).closest('.category-row');
                    $row.slideUp(200, function() {
                        $(this).remove();
                        toggleRemoveButtons();
                    });
                }
            });

            function toggleRemoveButtons() {
                if ($('.category-row').length > 1) {
                    $('.remove-row').removeClass('d-none');
                } else {
                    $('.remove-row').addClass('d-none');
                }
            }

            $('#CategorySubmit').on('submit', function(e) {
                e.preventDefault();
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true);
                submitBtn.find('.spinner-border').removeClass('d-none');
                $('.category-input').removeClass('is-invalid');

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('document.category.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.status) {
                            $('#categoryModal').modal('hide');
                            $('#CategorySubmit')[0].reset();
                            $('.category-row:not(:first)').remove();
                            toggleRemoveButtons();
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });

                            if (response.data && response.data.length > 0) {
                                $('#categoriesContainer .empty-state').remove();
                                let currentCount = $('.category-wrapper').length;

                                let newHtml = '';
                                response.data.forEach(cat => {
                                    newHtml += generateCategoryCard(cat, currentCount++);
                                });
                                $('#categoriesContainer').prepend(newHtml);
                            } else {
                                loadCategories();
                            }
                        }
                    },
                    error: function(xhr) {
                        iziToast.error({ title: 'Error', message: 'Failed to create categories.', position: 'topRight' });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.spinner-border').addClass('d-none');
                    }
                });
            });

            $('#reloadBtn').on('click', function() {
                const $icon = $(this).find('i');
                $icon.addClass('fa-spin');
                setTimeout(() => $icon.removeClass('fa-spin'), 1000);
                loadCategories();
            });

            function loadCategories() {
                $.ajax({
                    url: "{{ route('document.category.get') }}",
                    type: "GET",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    beforeSend: function() {
                        $('#categoriesContainer').html(`
                            <div class="col-12 text-center my-4 py-4">
                                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem; border-width: .2em;"></div>
                                <h6 class="text-slate-500 mt-2 fs-14 fw-semibold">Loading Directory...</h6>
                            </div>
                        `);
                    },
                    success: function(response) {
                        if (response.status && response.categories.length > 0) {
                            canEditGlobal = response.canUpdate;
                            canDeleteGlobal = response.canDelete;

                            let html = '';
                            response.categories.forEach((category, index) => {
                                html += generateCategoryCard(category, index);
                            });
                            $('#categoriesContainer').html(html);
                        } else {
                            showEmptyState();
                        }
                    },
                    error: function() {
                        iziToast.error({ title: 'Error', message: 'Failed to load categories', position: 'topRight' });
                        $('#categoriesContainer').html(`
                            <div class="col-12 text-center py-4">
                                <div class="mb-2"><i class="fa fa-exclamation-triangle text-danger opacity-50" style="font-size: 36px;"></i></div>
                                <h6 class="fw-bold text-dark mb-0 fs-14">Error loading directory</h6>
                                <button class="btn btn-sm btn-outline-primary mt-2 py-1 fs-12" onclick="loadCategories()">Try Again</button>
                            </div>
                        `);
                    }
                });
            }

            $(document).on('click', '.edit-category', function() {
                const encryptedId = $(this).data('id');
                const $card = $(this).closest('.folder-card');
                const $container = $card.find('.category-title-container');
                const currentName = $container.find('.category-name-text').text().trim();
                const originalHtml = $container.html();

                $container.html(`
                    <div class="input-group mt-1 shadow-sm rounded-2">
                        <input type="text" class="form-control form-control-sm inline-edit-input fw-semibold" value="${currentName}" id="categoryNameInput_${encryptedId}">
                        <button class="btn btn-primary btn-sm save-rename-btn px-2" type="button" title="Save" style="border-radius: 0 6px 6px 0;"><i class="fa fa-check"></i></button>
                    </div>
                    <div class="text-end mt-1">
                        <a href="javascript:void(0)" class="text-muted fs-11 text-decoration-none cancel-rename-btn hover-primary">Cancel</a>
                    </div>
                `);

                const $input = $(`#categoryNameInput_${encryptedId}`);
                $input.focus().select();

                $container.find('.save-rename-btn').on('click', function() {
                    const newName = $input.val().trim();
                    if (newName && newName !== currentName) {
                        updateCategoryName(encryptedId, newName, $container, originalHtml, $card);
                    } else {
                        $container.html(originalHtml);
                    }
                });

                $container.find('.cancel-rename-btn').on('click', function() {
                    $container.html(originalHtml);
                });

                $input.on('keyup', function(e) {
                    if (e.key === 'Enter') $container.find('.save-rename-btn').click();
                    else if (e.key === 'Escape') $container.find('.cancel-rename-btn').click();
                });
            });

            function updateCategoryName(encryptedId, newName, $container, originalHtml, $card) {
                $container.html(`<div class="text-start py-1"><div class="spinner-border spinner-border-sm text-primary" style="width: 1rem; height: 1rem;"></div> <span class="ms-1 fs-12 text-muted fw-semibold">Saving...</span></div>`);

                $.ajax({
                    url: "{{ route('document.category.update') }}",
                    type: "GET",
                    data: { encrypted_id: encryptedId, d_category: newName },
                    success: function(response) {
                        if (response.status) {
                            iziToast.success({ title: 'Saved', message: response.message, position: 'topRight' });

                            let $updatedHtml = $(originalHtml);
                            $updatedHtml.find('.category-name-text').text(newName).attr('title', newName);
                            $container.html($updatedHtml);

                            $card.find('.delete-category').data('name', newName);
                        } else {
                            $container.html(originalHtml);
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                        }
                    },
                    error: function() {
                        $container.html(originalHtml);
                        iziToast.error({ title: 'Error', message: 'Failed to rename category', position: 'topRight' });
                    }
                });
            }

            $(document).on('click', '.delete-category', function() {
                const categoryId = $(this).data('id');
                const categoryName = $(this).data('name');
                const $wrapperRow = $(this).closest('.category-wrapper');

                iziToast.question({
                    timeout: 20000, close: false, overlay: true, displayMode: 'once',
                    id: 'question', zindex: 999, title: 'Confirm Deletion',
                    message: `<div class="mt-2 text-slate-600 fs-13">Are you sure you want to delete <br><b class="text-danger fs-14">"${categoryName}"</b>?</div>`,
                    position: 'center',
                    buttons: [
                        ['<button class="btn btn-danger btn-sm text-white border-0 px-3 py-1 rounded-pill fw-semibold shadow-sm">Delete</button>', function(instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                            $.ajax({
                                url: "{{ route('document.category.delete') }}",
                                type: "POST",
                                data: { id: categoryId, _token: $('meta[name="csrf-token"]').attr('content') },
                                success: function(response) {
                                    if (response.status) {
                                        iziToast.success({ title: 'Deleted', message: response.message, position: 'topRight' });

                                        $wrapperRow.css('transform', 'scale(0.9)').fadeOut(300, function() {
                                            $(this).remove();
                                            if ($('.category-wrapper').length === 0) {
                                                showEmptyState();
                                            }
                                        });

                                    } else {
                                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                                    }
                                },
                                error: function() {
                                    iziToast.error({ title: 'Error', message: 'Failed to delete category', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button class="btn btn-light btn-sm px-3 py-1 rounded-pill fw-semibold">Cancel</button>', function(instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

        });
    </script>
</body>
</html>
