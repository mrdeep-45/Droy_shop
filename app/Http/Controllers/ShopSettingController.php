<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopSettingController extends Controller
{
    /**
     * Show shop settings form.
     */
    public function index()
    {
        $settings = DB::table('tbl_shop_settings')->pluck('value', 'key');
        return view('shop.setting.index', compact('settings'));
    }

    /**
     * Save shop settings.
     */
    public function save(Request $request)
    {
        $fields = [
            'shop_name', 'shop_email', 'shop_phone', 'shop_address',
            'currency_symbol', 'currency_code', 'tax_rate',
            'free_shipping_above', 'low_stock_threshold',
            'meta_title', 'meta_description',
            'facebook_url', 'instagram_url', 'twitter_url',
            'whatsapp_number', 'razorpay_key', 'razorpay_secret',
            'cod_enabled', 'order_prefix',
        ];

        foreach ($fields as $key) {
            if ($request->has($key)) {
                DB::table('tbl_shop_settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $request->input($key), 'updated_at' => now()]
                );
            }
        }

        // Handle logo upload
        if ($request->hasFile('shop_logo')) {
            $file = $request->file('shop_logo');
            $filename = 'shop_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/shop'), $filename);
            DB::table('tbl_shop_settings')->updateOrInsert(
                ['key' => 'shop_logo'],
                ['value' => 'uploads/shop/' . $filename, 'updated_at' => now()]
            );
        }

        return response()->json(['success' => true, 'message' => 'Settings saved successfully.']);
    }
}
