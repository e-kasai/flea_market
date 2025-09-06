<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ExhibitController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PurchaseController;

//商品一覧画面表示(マイリストを呼ぶメソッドはコントローラー内で条件分岐呼び出し)
Route::get('/', [ItemController::class, 'index'])->name('items.index');



//ユーザー登録
//ログイン済みユーザーは登録ページにアクセスできないようにmiddleware('guest')を追加
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

//プロフィール関連
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

//出品
Route::prefix('sell')
    ->middleware('auth')
    ->group(function () {
        //出品画面表示
        Route::get('/', [ExhibitController::class, 'showExhibitForm'])->name('exhibit.show');
        //出品処理
        Route::post('/', [ExhibitController::class, 'storeExhibitItem'])->name('exhibit.store');
    });

//商品詳細
// Route::get('/item/{item_id}', [ItemController::class, 'showItemDetail'])
//     ->name('details.show');

//商品詳細
Route::get('/item/{item}', [ItemController::class, 'showItemDetail'])
    ->name('details.show');


//コメント、いいね機能
// Route::prefix('item')
//     ->middleware('auth')
//     ->group(function () {
//         //コメント
//         Route::post('/{item_id}/comment', [CommentController::class, 'storeComment'])
//             ->name('comments.store');
//         //いいね追加
//         Route::post('/{item_id}/favorite', [FavoriteController::class, 'setItemFavorite'])
//             ->name('favorite.store');
//         //いいね解除
//         Route::delete('/{item_id}/favorite', [FavoriteController::class, 'setItemUnfavorite'])
//             ->name('favorite.destroy');
//     });


//コメント、いいね機能
Route::prefix('item')
    ->middleware('auth')
    ->group(function () {
        //コメント
        Route::post('/{item}/comment', [CommentController::class, 'storeComment'])
            ->name('comments.store');
        //いいね追加
        Route::post('/{item}/favorite', [FavoriteController::class, 'setItemFavorite'])
            ->name('favorite.store');
        //いいね解除
        Route::delete('/{item}/favorite', [FavoriteController::class, 'setItemUnfavorite'])
            ->name('favorite.destroy');
    });




//購入機能
//購入画面表示
// Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchasePage'])->name('purchase.show')->middleware('auth');
Route::get('/purchase/{item}', [PurchaseController::class, 'showPurchasePage'])->name('purchase.show')->middleware('auth');
