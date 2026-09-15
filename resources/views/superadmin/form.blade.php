<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
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
                        <div class="card custom-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title text-primary mb-0">
                                    {{ $page_name }}
                                </div>
                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                                                href="#lead-list" aria-selected="true"><i class="fas fa-list me-1"></i>
                                                List</a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                                href="#new-lead" aria-selected="true"><i class="fas fa-plus me-1"></i>
                                                New Lead</a>
                                        </li>
                                    @endif
                                </ul>

                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                        <div class="tab-pane fade show active" id="listTab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered crud-table" width="100%">
                                                    <thead>
                                                        <tr>

                                                            {{-- Action Column --}}
                                                            @if(permissions_check('canUpdate') || permissions_check('canDelete'))
                                                                <th class="text-center">Actions</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if(permissions_check('canCreate'))
                                        <div class="tab-pane text-muted" id="about-footer" role="tabpanel">

                                        </div>
                                    @endif
                                </div>
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
    <script>

    </script>
</body>

</html>
