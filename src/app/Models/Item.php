<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;
use App\Models\Category;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        //単体テストのユースケースはリレーション経由で出品する場合のみの為seller_idは除外
        'item_name',
        'brand_name',
        'price',
        'color',
        'condition',
        'description',
        'image_path',
        'is_sold',
    ];

    protected $casts = [
        'price' => 'integer',
        'condition' => 'integer',
        'is_sold' => 'boolean',
    ];

    // itemはUserに属する(1対多) = belongsTo
    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Itemは複数のCommentを持つ = hasMany
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    // 1つの商品は0または1件の取引を持つ = hasOne
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    //中間テーブルcategory_itemのリレーション
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id');
    }

    //中間テーブルfavoritesのリレーション
    // usersだとメソッド内容がわかりにくいのでfavoritedByUsersと命名
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id');
    }
}
