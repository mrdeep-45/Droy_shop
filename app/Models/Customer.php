<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'tbl_customer';

    protected $primaryKey = 'customer_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'company_name',
        'customer_type',
        'contact_person',
        'email',
        'phone',
        'alt_phone',
        'business_registration_no',
        'tax_id',
        'gst_no',
        'pan_no',
        'gst_registration_type',
        'billing_address',
        'shipping_address',
        'is_same_as_billing',
        'account_status',
        'status',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'created_by',
        'updated_by',
        'notes',
    ];

    protected $casts = [
        'is_same_as_billing' => 'boolean',
        'status' => 'boolean',
    ];

    public function cityRelation()
    {
        return $this->belongsTo(\App\Models\CityModel::class, 'city_id', 'city_id');
    }

    public function stateRelation()
    {
        return $this->belongsTo(\App\Models\StateModel::class, 'state_id', 'state_id');
    }

    public function countryRelation()
    {
        return $this->belongsTo(\App\Models\CountryModel::class, 'country_id', 'c_id');
    }

    public function leads()
    {
        return $this->hasMany(\App\Models\EnquiryManageModel::class, 'customer_id', 'customer_id');
    }

    public function deals()
    {
        return $this->hasMany(\App\Models\Deal::class, 'customer_id', 'customer_id');
    }
}
