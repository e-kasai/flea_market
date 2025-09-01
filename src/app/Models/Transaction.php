<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'shipping_postal_code',
        'shipping_address',
        'shipping_building',
    ];

    protected $casts = [
        'payment_method' => 'integer',
    ];

    //取引は必ず１人のユーザー（購入者）に属する = belongsTo
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //取引は必ず１つの商品に属する = belongsTo
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
