<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    //
    use HasFactory;
    protected $table = 'mst_city';
    protected $primaryKey = 'city_id';
    protected $fillable = ['city_id', 'c_id', 'state_id', 'city_name', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'];


    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }
    public function country()
    {
        return $this->belongsTo(CountryModel::class, 'c_id', 'c_id');
    }

    public function state()
    {
        return $this->belongsTo(StateModel::class, 'state_id', 'state_id');
    }
}
