<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WidgetPack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['amount'];

    /** Relationships */
    public function orderLinkers() {
        return $this->hasMany(OrderPackLinker::class, 'pack_id', 'id');
    }
}
