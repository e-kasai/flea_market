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
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => auth()->id()]);
        // 出品商品
        $items = $user->items()->latest()->get();
        //購入商品
        $transactions = $user->transactions()->with('item')->get();
        $purchasedItems = $transactions->pluck('item')->filter();
        return view('profile', compact('profile', 'user', 'items', 'purchasedItems'));
    }

    //プロフィール編集画面の表示
    public function showProfileEditPage()
    {
        //profilesテーブルから、user_id=ログインユーザーのレコードを探しなければ新しいインスタンスを作成
        $profile = Profile::firstOrNew(['user_id' => auth()->id()]);
        $user = Auth::user();
        return view('edit_profile', compact('profile', 'user'));
    }

    //プロフィール更新処理
    public function updateProfile(ProfileRequest $request)
    {
        //ユーザー名更新(usersテーブルのnameカラム)
        //transactionでプロフィールデータが１部だけ変更されてしまうのを防ぐ(All or nothing)

        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $user = auth()->user();
            $user->update(['name' => $validated['name']]);

            // プロフィール取得 or 新規生成
            $profile = Profile::firstOrNew(['user_id' => auth()->id()]);

            //プロフィール画像アップロードの処理

            //アップロード済み画像のパスを$oldPathに格納しておく
            $oldPath = $profile->avatar_path;
            //ファイルがフォームにありuploadが正常完了したら
            if ($request->hasFile('avatar_path') && $request->file('avatar_path')->isValid()) {
                // 新しい画像を保存
                $path = $request->file('avatar_path')->store('material_images', 'public');
                $profile->avatar_path = $path;
            }

            $profile->fill([
                'postal_code' => $validated['postal_code'] ?? null,
                'address'     => $validated['address'] ?? null,
                'building'    => $validated['building'] ?? null,
            ]);
            //ここで画像やusername,他情報が"DB"に保存される
            $profile->save();

            // コミット成功後だけ古い画像を削除（ロールバック時は実行されない）
            if ($oldPath && $oldPath !== $profile->avatar_path) {
                DB::afterCommit(function () use ($oldPath) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                });
            }
        });

        return redirect()->route('profile.show')->with('message', 'プロフィールを更新しました。');
    }
}
