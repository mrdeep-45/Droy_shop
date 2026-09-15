<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatModel extends Model
{
    //
     use HasFactory;
    protected $table = 'mst_vat';

    protected $primaryKey = 'vat_id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = ['vat_id', 'vat_no', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];


    public function scopeActive($query)
    {
        return $query->where('status', 0)->orderByRaw('CAST(vat_no AS UNSIGNED) ASC');
    }
}
