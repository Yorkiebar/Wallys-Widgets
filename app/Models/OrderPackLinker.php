<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackLinker extends Model
{
    use HasFactory;

    /** Relationships */
    public function order() {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function pack() {
        return $this->hasOne(WidgetPack::class, 'id', 'pack_id');
    }
}
