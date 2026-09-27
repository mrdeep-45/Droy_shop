{{-- Loader overlay --}}
<div id="shopLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.35); z-index:99999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:2rem 3rem; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0 fw-semibold text-dark">Please wait...</p>
    </div>
</div>
<script>
    function showShopLoader() { document.getElementById('shopLoader').style.display = 'flex'; }
    function hideShopLoader() { document.getElementById('shopLoader').style.display = 'none'; }
</script>
