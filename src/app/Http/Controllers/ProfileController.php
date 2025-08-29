<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //プロフィール画面の表示
    public function showProfilePage()
    {
        $userId = Auth::id(); // ログイン中ユーザーのID取得
        // withメソッド(EagerLoad)でProfileモデルに関連付けられたUser情報も一緒に取得
        $profile = Profile::with('user')
            // user_idカラム=$userIdがあるか
            ->where('user_id', $userId)
            //最初の１件だけ取得
            ->first();
        return view('profile', compact('profile'));
    }

    //プロフィール編集画面の表示
    public function showProfileEditPage()
    {
        return view('edit_profile');
    }
}
