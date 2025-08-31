<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //プロフィール画面の表示
    public function showProfilePage()
    {
        //User モデル経由で $profile を取得（ログイン中のユーザー１人分の情報）
        $currentUser = Auth::user()->profile;
        $userProfile = Profile::firstOrNew(['user_id' => auth()->id()]);
        return view('profile', compact('userProfile', 'currentUser'));
    }

    //プロフィール編集画面の表示
    public function showProfileEditPage()
    {
        //profilesテーブルから、user_id=ログインユーザーのレコードを探しなければ新しいインスタンスを作成
        $userProfile = Profile::firstOrNew(['user_id' => auth()->id()]);
        $currentUser = Auth::user();
        return view('edit_profile', compact('userProfile', 'currentUser'));
    }

    //プロフィール更新処理
    public function updateProfile(ProfileRequest $request)
    {
        //ユーザー名更新(usersテーブルのnameカラム)
        //transactionでプロフィールデータが１部だけ変更されてしまうのを防ぐ(All or nothing)

        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $userProfile = auth()->user();
            $userProfile->update(['name' => $validated['name']]);

            // プロフィール取得 or 新規生成
            $userProfile = Profile::firstOrNew(['user_id' => auth()->id()]);

            //プロフィール画像アップロードの処理

            //アップロード済み画像のパスを$oldPathに格納しておく
            $oldPath = $userProfile->avatar_path;
            //ファイルがフォームにありuploadが正常完了したら
            if ($request->hasFile('avatar_path') && $request->file('avatar_path')->isValid()) {
                // 新しい画像を保存
                $path = $request->file('avatar_path')->store('material_images', 'public');
                $userProfile->avatar_path = $path;
            }

            //郵便番号,住所,建物名
            $userProfile->fill([
                'postal_code' => $validated['postal_code'] ?? null,
                'address'     => $validated['address'] ?? null,
                'building'    => $validated['building'] ?? null,
            ]);
            //ここで画像やusername,他情報が"DB"に保存される
            $userProfile->save();

            // コミット成功後だけ古い画像を削除（ロールバック時は実行されない）
            if ($oldPath && $oldPath !== $userProfile->avatar_path) {
                DB::afterCommit(function () use ($oldPath) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                });
            }
        });
        return redirect()->route('profile.show')->with('message', 'プロフィールを更新しました。');
    }
}
