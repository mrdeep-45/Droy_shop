<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopNotificationModel extends Model
{
    protected $table      = 'tbl_shop_notifications';
    protected $primaryKey = 'notification_id';
    public $incrementing  = true;
    public $timestamps    = false;

    protected $casts = [
        'data'       => 'array',
        'is_read'    => 'boolean',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'user_type', 'user_id', 'title', 'message',
        'type', 'icon', 'color', 'data', 'link',
        'is_read', 'read_at', 'created_at',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }
}
