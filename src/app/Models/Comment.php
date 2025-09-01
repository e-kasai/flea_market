<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    //単体テストのユースケースはリレーション経由でコメントする場合のみの為外部キーは除外
    protected $fillable = [
        'body',
    ];

    // Comment(Child)はItem(Parent)に属する = belongsTo
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Comment(Child)はUser(Parent)に属する = belongsTo
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
