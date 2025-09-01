<?php

namespace App\Models;

//主側のモデルクラスのインポート
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'address',
        'building',
        'avatar_path',
    ];

    // プロフィールは必ず１人のuserに属する = belongsTo
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
