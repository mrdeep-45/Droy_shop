<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsMaster extends Model
{
    protected $table = 'tbl_terms';

    protected $primaryKey = 'term_id';

    public $timestamps = false;

    protected $fillable = [
        'module',
        'terms',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
}
