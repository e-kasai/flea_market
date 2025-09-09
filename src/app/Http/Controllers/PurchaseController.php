<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingAddressRequest;
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
        $originalAddress = [
            'postal_code' => $profile?->postal_code,
            'address'     => $profile?->address,
            'building'    => $profile?->building,
        ];
        // UI側で購入ボタンを無効化するためのフラグ
        $canPurchase            = (bool) $profile;

        // セッションに保存されていなければ新しいから配列を返す
        $draftAddress = session("draft_address.{$item->id}", []);

        // 想定キーだけに限定（予期せぬキーの混入防止）
        $draftAddress = array_intersect_key($draftAddress, $originalAddress);

        // 差分merge：draftAddressが優先（buildingはnull時、nullで上書きOK）
        $shipping = array_replace($originalAddress, $draftAddress);

        return view('purchase', compact('item', 'profile', 'shipping', 'canPurchase'));
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
        // 売れたものは買えない
        if ($item->is_sold ?? false) {
            return back()->with('message', 'この商品は売り切れです。');
        }

        //配送先変更あればmerge
        $originalAddress = [
            'postal_code' => $profile?->postal_code,
            'address'     => $profile?->address,
            'building'    => $profile?->building,
        ];

        $draftAddress = session("draft_address.{$item->id}", []);
        $draftAddress = array_intersect_key($draftAddress, $originalAddress);
        $shipping = array_replace($originalAddress, $draftAddress);

        //購入処理
        DB::transaction(
            function () use ($item, $user, $shipping, $request) {
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
                    'shipping_postal_code'  => $shipping['postal_code'],
                    'shipping_address'      => $shipping['address'],
                    'shipping_building'     => $shipping['building'],
                ]);
                $locked->update(['is_sold' => true]);
            }
        );
        // 使い終わったドラフトは掃除
        session()->forget("draft_address.{$item->id}");

        return redirect()->route('items.index')
            ->with('message', '購入が完了しました。');
    }

    // 配送先変更画面表示
    public function showShippingAddress(Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile;

        $draft = session("draft_address.{$item->id}", []);

        $form = [
            'postal_code' => $draft['postal_code'] ?? $profile?->postal_code,
            'address'     => $draft['address']     ?? $profile?->address,
            'building'    => $draft['building']    ?? $profile?->building,
        ];

        return view('shipping_address', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    // 配送先変更
    public function updateShippingAddress(ShippingAddressRequest $request, Item $item)
    {

        $validated = $request->validated();

        //sessionに新しい配送先を一時保存 key名= draft_address.idの値
        session()->put(
            "draft_address.{$item->id}",
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'] ?? null,
            ]
        );

        return redirect()
            ->route('purchase.show', $item)
            ->with('message', '配送先を変更しました');
    }
}
