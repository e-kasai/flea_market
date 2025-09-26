<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ExhibitController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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
    ->middleware(['auth', 'verified'])
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
    ->middleware(['auth', 'verified'])
    ->group(function () {
        //出品画面表示
        Route::get('/', [ExhibitController::class, 'showExhibitForm'])->name('exhibit.show');
        //出品処理
        Route::post('/', [ExhibitController::class, 'storeExhibitItem'])->name('exhibit.store');
    });


//商品詳細
Route::get('/item/{item}', [ItemController::class, 'showItemDetail'])
    ->name('details.show');


//コメント機能
Route::prefix('item')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::post('/{item}/comment', [CommentController::class, 'storeComment'])
            ->name('comments.store');
    });

//いいね機能
Route::prefix('item')
    ->group(function () {
        Route::post('/{item}/favorite', [FavoriteController::class, 'setItemFavorite'])
            ->name('favorite.store');
        Route::delete('/{item}/favorite', [FavoriteController::class, 'setItemUnfavorite'])
            ->name('favorite.destroy');
    });


//stripe決済画面へ遷移
Route::post('/checkout/{item}', [PurchaseController::class, 'startPayment'])
    ->name('stripe.checkout.create')
    ->middleware(['auth', 'verified']);

//stripe決済処理確定
Route::get('/purchase/complete', [PurchaseController::class, 'finalizeTransaction'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.complete');

//購入画面表示
Route::prefix('purchase')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/{item}', [PurchaseController::class, 'showPurchasePage'])->name('purchase.show');
        Route::post('/{item}', [PurchaseController::class, 'purchaseItem'])->name('purchase.item');
    });

//配送先変更
Route::prefix('purchase')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/address/{item}', [PurchaseController::class, 'showShippingAddress'])->name('address.show');
        Route::patch('/address/{item}', [PurchaseController::class, 'updateShippingAddress'])->name('address.update');
    });


// 未認証ユーザーに見せる認証案内ページ
Route::get('/email/verify', function () {
    return view('auth.verify_email');
})->middleware('auth')->name('verification.notice');


// メール認証リンクアクセス時
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('items.index');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');


// 認証メールの再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
