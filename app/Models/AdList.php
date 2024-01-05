<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AdList extends Model
{
    protected $table = 'ad_list';

    protected $fillable = ['product_id', 'user_id', 'img', 'data'];
}
