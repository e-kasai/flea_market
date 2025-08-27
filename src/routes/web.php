<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;

//ユーザー（管理者）登録
//ログイン済みユーザーは登録ページにアクセスできないようにmiddleware('guest')を追加
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

//プロフィール画面表示
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [ProfileController::class, 'showProfilePage'])->name('profile.show');
});


