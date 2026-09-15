<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'company_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // protected static function booted()
    // {
    //     static::created(function ($company) {
    //         \App\Models\CompanyModelEnli::create($company->toArray());
    //     });

    //     static::updated(function ($company) {
    //         \App\Models\CompanyModelEnli::updateOrCreate(
    //             ['email' => $company->email],
    //             $company->toArray()
    //         );
    //     });

    //     static::deleted(function ($company) {
    //         \App\Models\CompanyModelEnli::where('email', $company->email)->delete();
    //     });
    // }

    protected $fillable = [
        'name',
        'email',
        'password',
        'end_date',
        'plan_type',
        'start_date',
        'contact_no',
        'logo',
        'favicon',
        'project_name',
        'registration_number',
        'company_type',
        'industry',
        'founded_date',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'domain',
        'tax_id',
        'description',
        'signature',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'account_type',
        'upi_id',
        'upi_qr_code',
        'contact_phone',
        'contact_email',
        'gst_no',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'founded_date' => 'date',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 0,
    ];
    public function permissions()
    {
        return $this->hasMany(Company_permission_Model::class, 'company_id', 'company_id');
    }
}
