<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopAddressController extends Controller
{
    /**
     * Show customer address book.
     */
    public function index()
    {
        $customerId = session('customer_id');
        $addresses  = DB::table('tbl_shop_addresses')
            ->where('customer_id', $customerId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('store.account.address', compact('addresses'));
    }

    /**
     * Save a new address.
     */
    public function store(Request $request)
    {
        $customerId = session('customer_id');

        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
        ]);

        // If marked as default, unset other defaults
        if ($request->is_default) {
            DB::table('tbl_shop_addresses')
                ->where('customer_id', $customerId)
                ->update(['is_default' => 0]);
        }

        DB::table('tbl_shop_addresses')->insert([
            'customer_id' => $customerId,
            'name'        => $request->name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'state'       => $request->state,
            'pincode'     => $request->pincode,
            'country'     => $request->country ?? 'India',
            'is_default'  => $request->is_default ? 1 : 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Address saved successfully.']);
    }

    /**
     * Delete an address.
     */
    public function destroy(Request $request)
    {
        $customerId = session('customer_id');
        DB::table('tbl_shop_addresses')
            ->where('id', $request->address_id)
            ->where('customer_id', $customerId)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Address removed.']);
    }

    /**
     * Set an address as default.
     */
    public function setDefault(Request $request)
    {
        $customerId = session('customer_id');
        DB::table('tbl_shop_addresses')
            ->where('customer_id', $customerId)
            ->update(['is_default' => 0]);

        DB::table('tbl_shop_addresses')
            ->where('id', $request->address_id)
            ->where('customer_id', $customerId)
            ->update(['is_default' => 1]);

        return response()->json(['success' => true, 'message' => 'Default address updated.']);
    }
}
