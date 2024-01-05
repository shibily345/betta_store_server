<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';
    protected $fillable = [
        'data',
        'status',
        'user_id',          
        'vendor_id',   
        'tracking_id',      
    ];
}
