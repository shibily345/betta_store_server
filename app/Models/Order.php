<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    

    protected $casts = [
        'order_amount' => 'float',
        'total_tax_amount' => 'float',
        'product_id' => 'float',
        'quantity' => 'integer',
        'male_quantity' => 'integer',
        'female_quantity' => 'integer',
        'delivery_charge' => 'float',
        'user_id' => 'integer',
        'details_count' => 'integer', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime'  
    ];

    // public function setDeliveryChargeAttribute($value)
    // {
    //     $this->attributes['delivery_charge'] = round($value, 3);
    // }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}