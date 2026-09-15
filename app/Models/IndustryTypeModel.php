<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryTypeModel extends Model
{
    //
    use HasFactory;

    protected $table = 'mst_industry_type';
    protected $primaryKey = 'industry_type_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'industry_type_id', 
        'industry_type_name', 
        'status', 
        'created_by', 
        'created_at', 
        'updated_by', 
        'updated_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0)->orderBy('industry_type_name', 'ASC');
    }
}
