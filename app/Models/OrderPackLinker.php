<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackLinker extends Model
{
    use HasFactory;

    public $fillable = ['order_id', 'pack_id', 'quantity'];

    /** Relationships */
    public function order() {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function pack() {
        return $this->hasOne(WidgetPack::class, 'id', 'pack_id');
    }

    public function getPack() {
        return $this->pack ?: WidgetPack::withTrashed()->where('id', $this->pack_id)->first();
    }
}
