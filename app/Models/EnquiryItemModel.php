<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryItemModel extends Model
{
    protected $table = 'tbl_enquiry_items';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'enquiry_id',
        'product_id',
        'quantity',
        'price',
        'gst',
    ];

    public function enquiry()
    {
        return $this->belongsTo(EnquiryManageModel::class, 'enquiry_id', 'enquiry_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }
}
