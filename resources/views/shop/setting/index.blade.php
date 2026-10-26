<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>Shop Settings — HexaShop Admin</title>
    @include('shop.include.header_link')
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header">
        <div>
            <h4><i class="bx bx-cog text-shop-primary me-2"></i>Shop Settings</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <span>Settings</span>
            </div>
        </div>
    </div>

    <div class="px-3 pb-4">
        <form id="settingsForm">
            @csrf
            <div class="row g-4">

                {{-- General --}}
                <div class="col-lg-6">
                    <div class="shop-card h-100">
                        <div class="card-header"><i class="bx bx-store me-2 text-primary"></i>General</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Shop Name</label>
                                <input type="text" name="shop_name" class="form-control" value="{{ $settings['shop_name'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shop Email</label>
                                <input type="email" name="shop_email" class="form-control" value="{{ $settings['shop_email'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shop Phone</label>
                                <input type="text" name="shop_phone" class="form-control" value="{{ $settings['shop_phone'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shop Address</label>
                                <textarea name="shop_address" class="form-control" rows="2">{{ $settings['shop_address'] ?? '' }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Order Prefix</label>
                                <input type="text" name="order_prefix" class="form-control" value="{{ $settings['order_prefix'] ?? 'ORD' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shop Logo</label>
                                @if(!empty($settings['shop_logo']))
                                    <div class="mb-2"><img src="{{ asset($settings['shop_logo']) }}" height="50" alt="Logo"></div>
                                @endif
                                <input type="file" name="shop_logo" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Currency & Tax --}}
                <div class="col-lg-6">
                    <div class="shop-card mb-4">
                        <div class="card-header"><i class="bx bx-money me-2 text-primary"></i>Currency & Tax</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? '₹' }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Currency Code</label>
                                    <input type="text" name="currency_code" class="form-control" value="{{ $settings['currency_code'] ?? 'INR' }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Tax Rate (%)</label>
                                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ $settings['tax_rate'] ?? '18' }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Free Shipping Above (₹)</label>
                                    <input type="number" name="free_shipping_above" class="form-control" value="{{ $settings['free_shipping_above'] ?? '500' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Low Stock Alert (qty)</label>
                                    <input type="number" name="low_stock_threshold" class="form-control" value="{{ $settings['low_stock_threshold'] ?? '5' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shop-card">
                        <div class="card-header"><i class="bx bx-share-alt me-2 text-primary"></i>Social & WhatsApp</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Facebook URL</label>
                                <input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instagram URL</label>
                                <input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" class="form-control" value="{{ $settings['whatsapp_number'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO --}}
                <div class="col-lg-6">
                    <div class="shop-card">
                        <div class="card-header"><i class="bx bx-search-alt me-2 text-primary"></i>SEO</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="{{ $settings['meta_title'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3">{{ $settings['meta_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="col-lg-6">
                    <div class="shop-card">
                        <div class="card-header"><i class="bx bx-credit-card me-2 text-primary"></i>Payment</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Razorpay Key</label>
                                <input type="text" name="razorpay_key" class="form-control" value="{{ $settings['razorpay_key'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Razorpay Secret</label>
                                <input type="password" name="razorpay_secret" class="form-control" placeholder="Leave blank to keep current">
                            </div>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="cod_enabled" id="codEnabled" value="1"
                                    {{ ($settings['cod_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="codEnabled">Enable Cash on Delivery (COD)</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bx bx-save me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

</main>

@include('shop.include.footer_link')

<script>
$('#settingsForm').on('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    $.ajax({
        url: '{{ route("shop.setting.save") }}',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(res) {
            Swal.fire(res.success ? 'Saved!' : 'Error', res.message, res.success ? 'success' : 'error');
        },
        error: function() {
            Swal.fire('Error', 'Server error occurred.', 'error');
        }
    });
});
</script>
</body>
</html>
