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

    // 1つに属するので単数形のメソッド名を使う
    // このメソッド名をコントローラで with() の引数に指定する
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
