<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstModel extends Model
{
    //
      use HasFactory;
    protected $table = 'mst_gst';

    protected $primaryKey = 'gst_id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = ['gst_id', 'gst_no', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];


    public function scopeActive($query)
    {
        return $query->where('status', 0)->orderByRaw('CAST(gst_no AS UNSIGNED) ASC');
    }
}
