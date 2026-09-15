<?php

namespace App\Imports;

use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use App\Models\UnitModel;
use App\Models\GstModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty($row['product_name']) || empty($row['rate'])) {
                continue;
            }

            // Find Category ID by Name
            $categoryId = null;
            if (!empty($row['category'])) {
                $category = ProductCategoryModel::where('name', 'LIKE', trim($row['category']))->first();
                $categoryId = $category ? $category->category_id : null;
            }

            // Find Unit ID by Name
            $unitId = null;
            if (!empty($row['unit'])) {
                $unit = UnitModel::where('unit', 'LIKE', trim($row['unit']))->first();
                $unitId = $unit ? $unit->unit_id : null;
            }

            // Find GST ID by Percentage value (e.g. 18 or 18%)
            $gstId = null;
            if (!empty($row['gst'])) {
                $gstVal = preg_replace('/[^0-9.]/', '', $row['gst']); // strip % sign
                $gst = GstModel::where('gst_no', $gstVal)->first();
                $gstId = $gst ? $gst->gst_id : null;
            }

            // Generate Product Code (Auto Increment / Custom Prefix)
            $lastProduct = ProductModel::orderBy('product_id', 'desc')->first();
            $nextId = $lastProduct ? ($lastProduct->product_id + 1) : 1;
            $prodCode = 'PROD' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            ProductModel::create([
                'prod_code'   => $prodCode,
                'prod_name'   => trim($row['product_name']),
                'prod_amount' => $row['rate'],
                'category_id' => $categoryId,
                'unit_id'     => $unitId,
                'gst'         => $gstId,
                'prod_desc'   => $row['description'] ?? null,
                'created_by'  => Auth::id() ?? 1,
                'status'      => 0,
            ]);
        }
    }
}