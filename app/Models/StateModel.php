<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateModel extends Model
{
    //
    use HasFactory;
    protected $table = 'mst_state';
     protected $primaryKey = 'state_id';      // Tell Eloquent your primary key column
    protected $fillable = ['state_id','c_id','state_name','state_code', 'status', 'created_by', 'created_at','updated_by', 'updated_at'];


    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }
     public function countryGroup()
    {
        return $this->belongsTo(CountryModel::class, 'c_id', 'c_id');
    }
}
