<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_id', 'done'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
