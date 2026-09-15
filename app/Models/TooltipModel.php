<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TooltipModel extends Model
{
    use HasFactory;

    protected $table = 'mst_tooltip';

    protected $primaryKey = 'tooltip_id';

    protected $fillable = [
        'module_name',
        'field_key',
        'field_label',
        'tooltip_text',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0)
                     ->orderBy('module_name', 'asc');
    }
}
