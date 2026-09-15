<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryModel extends Model
{
    //
     use HasFactory;
    protected $table = 'mst_country';
    protected $primaryKey = 'c_id';      // Tell Eloquent your primary key column
    protected $fillable = ['c_id', 'country_name', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];


    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }
}
