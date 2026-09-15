<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteTitleModel extends Model
{
    //
    use HasFactory;

    protected $table = 'tbl_note_title';

    protected $primaryKey = 'note_title_id';

    protected $fillable = [
        'note_title',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0)
                     ->orderBy('note_title', 'asc');
    }
}
