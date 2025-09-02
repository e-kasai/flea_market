<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;

//商品一覧画面表示(マイリストを呼ぶメソッドはコントローラー内で条件分岐呼び出し)
Route::get('/', [ItemController::class, 'index'])->name('items.index');



//ユーザー登録
//ログイン済みユーザーは登録ページにアクセスできないようにmiddleware('guest')を追加
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});


Route::prefix('mypage')
    ->middleware('auth')
    ->group(function () {
        //プロフィール画面表示(出品、購入商品一覧を呼ぶメソッドはコントローラー内で条件分岐呼び出し)
        Route::get('/', [ProfileController::class, 'showProfilePage'])->name('profile.show');
        //プロフィール編集画面表示
        Route::get('/profile', [ProfileController::class, 'showProfileEditPage'])->name('profile.edit');
        //プロフィール更新
        Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    });
