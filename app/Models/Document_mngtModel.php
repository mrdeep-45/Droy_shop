<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document_mngtModel extends Model
{
    //
    use HasFactory;

    protected $table = 'document_mngt';

    protected $primaryKey = 'dm_id';
    protected $fillable   = [
        'batch_id',
        'dc_id',
        'certificate',
        'is_expiry_date',
        'expiry_date',
        'file_name',
        'assign_to_staff',
        'assigned_staff_ids',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'status',
    ];
    public function categoryRelation()
    {
        return $this->belongsTo(Document_categoryModel::class, 'category', 'dc_id');
    }
}
