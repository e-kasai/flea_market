<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Http\Requests\TransactionRequest;
use Illuminate\Support\Facades\DB;


class PurchaseController extends Controller
{
    public function showPurchasePage(Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // ログイン済みだがprofileなしパターンに対してnullセーフ
        $shipping_postal_code   = $profile?->postal_code;
        $shipping_address       = $profile?->address;
        $shipping_building      = $profile?->building;
        // UI側で購入ボタンを無効化するためのフラグ
        $canPurchase            = (bool) $profile;
        return view('purchase', compact('item', 'profile', 'shipping_postal_code', 'shipping_address', 'shipping_building', 'canPurchase'));
    }

    //購入処理
    public function purchaseItem(TransactionRequest $request, Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile;

        // プロフィール未登録なら編集ページへ
        if (! $profile) {
            return redirect()
                ->route('profile.edit')
                ->with('message', '購入には住所登録が必要です。プロフィールを設定してください。');
        }
        // 自分の商品は買えない
        if ($item->seller_id === $user->id) {
            return back()->with('message', '自分の商品は購入できません。');
        }
        // すでに売れたものは買えない
        if ($item->is_sold ?? false) {
            return back()->with('message', 'この商品は売り切れです。');
        }

        //上記のifにあてはまらない場合のみ購入処理
        DB::transaction(
            function () use ($item, $user, $profile, $request) {
                // 同時購入を防止
                $locked = Item::whereKey($item->id)->lockForUpdate()->first();

                if ($locked->is_sold) {
                    // 直前に売れた場合の二重購入防止
                    throw new \RuntimeException('この商品はすでに売り切れです。');
                }

                Transaction::create([
                    'item_id'               => $locked->id,
                    'buyer_id'              => $user->id,
                    'purchase_price'        => $locked->price,
                    'payment_method'        => (int) $request->input('payment_method'),
                    'shipping_postal_code'  => $profile->postal_code,
                    'shipping_address'      => $profile->address,
                    'shipping_building'     => $profile->building,
                ]);
                $locked->update(['is_sold' => true]);
            }
        );
        return redirect()->route('items.index')
            ->with('message', '購入が完了しました。');
    }
}
