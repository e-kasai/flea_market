<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingAddressRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Http\Requests\TransactionRequest;
use App\Http\Controllers\CheckoutController;


class PurchaseController extends Controller
{
    public function showPurchasePage(Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile;
        $shippingAddress = $this->buildShippingAddress($item, $profile);
        // UI側で購入ボタンを無効化するためのフラグ
        $canPurchase            = (bool) $profile;
        return view('purchase', compact('item', 'profile', 'shippingAddress', 'canPurchase'));
    }


    public function purchaseItem(TransactionRequest $request, Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if (! $profile) {
            return redirect()
                ->route('profile.edit')
                ->with('message', '購入には住所登録が必要です。プロフィールを設定してください。');
        }
        if ($item->seller_id === $user->id) {
            return back()->with('message', '自分の商品は購入できません。');
        }
        if ($item->is_sold ?? false) {
            return back()->with('message', 'この商品は売り切れです。');
        }
        //Stripeへ
        return app(CheckoutController::class)->startPayment($request, $item);
    }


    // 配送先変更画面表示
    public function showShippingAddress(Item $item)
    {
        $user = auth()->user();
        $profile = $user->profile;
        $shippingAddress = $this->buildShippingAddress($item, $profile);

        return view('shipping_address', [
            'item' => $item,
            'shippingAddress' => $shippingAddress,
        ]);
    }

    // 配送先変更
    public function updateShippingAddress(ShippingAddressRequest $request, Item $item)
    {
        $validated = $request->validated();

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


    // プライベートメソッド
    private function buildShippingAddress(Item $item, ?Profile $profile): array
    {
        // プロフィールの住所
        $originalAddress = [
            'postal_code' => $profile?->postal_code,
            'address'     => $profile?->address,
            'building'    => $profile?->building,
        ];

        // セッションにある住所のドラフト
        $draftAddress = session("draft_address.{$item->id}", []);
        // 想定外キーの排除
        $draftAddress = array_intersect_key($draftAddress, $originalAddress);
        // ドラフト優先でマージ
        return array_replace($originalAddress, $draftAddress);
    }
}
