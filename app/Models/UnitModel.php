<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitModel extends Model
{
    //
     use HasFactory;
    protected $table = 'tbl_unit';
     protected $primaryKey = 'unit_id'; 
    protected $fillable = ['unit_id', 'unit', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];


    public function scopeActive($query)
    {
        return $query->where('status', 0)->orderBy('unit', 'asc');
    }
}
