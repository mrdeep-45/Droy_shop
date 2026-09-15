<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsnGstMapModel extends Model
{
    //
    use HasFactory;

    protected $table = 'tbl_hsn_gst_mapping';
    protected $primaryKey = 'hg_id';
    protected $fillable = ['hsn_no', 'gst_id',  'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }
     public function gst()
    {
        return $this->belongsTo(GstModel::class, 'gst_id', 'gst_id');
    }
}
