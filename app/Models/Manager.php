<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $table = 'mst_manager';

    protected $primaryKey = 'manager_id';

    public $timestamps = true;

    protected $fillable = [
        'salute',
        'role_id',
        'manager_name',
        'manager_email',
        'company_id',
        'password',
        'phone',
        'address',
        'status',
        'created_by',
        'created_type',
        'updated_by',
    ];
    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id', 'role_id');
    }


}
