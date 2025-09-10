<?php

namespace App\Models;
// あとでメール認証実装時に追加今は不要
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profile;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Item;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User(Parent)は0または1つのProfile(Child)を持つ
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // User(Parent)は複数のItem(Child)を持つ = hasMany
    public function items()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    // User(Parent)は複数のComment(Child)を持つ = hasMany
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // User(Parent)は複数のTransaction(Child)を持つ = hasMany
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    //中間テーブルfavoritesのリレーション
    // itemsというメソッド名はすでにあるのでfavoriteItemsを使用
    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id');
    }
}
