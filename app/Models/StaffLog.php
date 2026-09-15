<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLog extends Model
{
    //
     protected $table = 'staff_logs';
    protected $primaryKey = 'stlog_id';
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'action',
        'description',
        'performed_by',
        'created_at'
    ];
    public function staff()
    {
        return $this->belongsTo(Staffmodel::class, 'staff_id', 'staff_id');
    }
}
