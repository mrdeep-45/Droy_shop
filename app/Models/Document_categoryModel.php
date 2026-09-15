<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document_categoryModel extends Model
{
    //
     use HasFactory;

    protected $table = 'document_category';

    protected $primaryKey = 'dc_id';
    protected $fillable   = [
        'd_category',
        'parent_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'status',
    ];
    public function documents()
    {
        return $this->hasMany(Document_mngtModel::class, 'dc_id', 'dc_id');
    }
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'dc_id');
    }

    // Define the relationship for subcategories (children)
    public function subcategories()
    {
        return $this->hasMany(self::class, 'parent_id', 'dc_id');
    }

    // Scope for fetching only top-level categories
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // You should probably add 'parent_id' to $fillable or $guarded if you're using mass assignment
    protected $guarded = ['dc_id'];
}
