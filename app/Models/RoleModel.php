<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'tbl_role';

    protected $primaryKey = 'role_id';

    public $timestamps = true;

    protected $fillable = [
        'role_name',
        'status',
        'created_at',
        'company_id',
        'created_by',
        'type',
        'updated_at',
        'updated_by'
    ];

    public static function getByType($type)
    {
        return self::where('type', $type)->get();
    }

    public static function managers()
    {
        return self::where('type', 'manager')->get();
    }

    public static function staff()
    {
        return self::where('type', 'staff')->get();
    }
}
