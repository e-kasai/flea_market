<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class CheckoutController extends Controller
{
    public function redirectToCheckout(Request $request, Item $item)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $user   = auth()->user();
        // まずUIから来た支払い方法を読む（1=konbini, 2=card）
        $method = (int) ($request->input('payment_method') ?? 2);

        // コンビニはここで即確定（SOLD & Transaction作成）
        if ($method === 1) {
            // 配送先：ドラフト優先 → プロフィール補完
            $draft   = session("draft_address.{$item->id}", []);
            $profile = $user->profile;
            $base = [
                'postal_code' => $profile?->postal_code,
                'address'     => $profile?->address,
                'building'    => $profile?->building,
            ];
            $shipping = array_replace($base, array_intersect_key($draft, $base));

            \DB::transaction(function () use ($item, $user, $shipping) {
                $locked = \App\Models\Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
                if ($locked->is_sold) {
                    throw new \RuntimeException('この商品はすでに売り切れです。');
                }
                \App\Models\Transaction::create([
                    'item_id'              => $locked->id,
                    'buyer_id'             => $user->id,
                    'purchase_price'       => $locked->price,
                    'payment_method'       => 1,
                    'shipping_postal_code' => $shipping['postal_code'] ?? null,
                    'shipping_address'     => $shipping['address'] ?? null,
                    'shipping_building'    => $shipping['building'] ?? null,
                    'is_paid'              => true,    // 即paid運用にするなら true
                ]);
                $locked->update(['is_sold' => true]);
            });
            // 住所ドラフト掃除
            session()->forget("draft_address.{$item->id}");
        }


        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->item_name],
                    'unit_amount' => (int) $item->price,
                ],
                'quantity' => 1,
            ]],
            // 成功時：session_id を必ず返させる
            'success_url' => route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            // 取消時：絶対URL推奨
            'cancel_url'  => route('details.show', $item, true),

            // 完了時に特定できるよう metadata を付与
            'payment_intent_data' => [
                'metadata' => [
                    'item_id'        => (string) $item->id,
                    'buyer_id'       => (string) auth()->id(),
                    'payment_method' => (string) ($request->input('payment_method') ?? '2'),
                ],
            ],

            // コンビニのオプション
            'payment_method_options' => [
                'konbini' => [
                    'expires_after_days' => 3,
                ],
            ],
        ]);

        return redirect($session->url);
    }


    public function complete(Request $request)
    {
        $sid = $request->query('session_id');
        abort_if(!$sid, 400, 'session_id missing');

        $stripe  = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sid, ['expand' => ['payment_intent']]);


        $pi      = $session->payment_intent;
        $methodMeta = (int)($pi->metadata->payment_method ?? 2); // 1=konbini, 2=card

        // カードは paid 必須／コンビニはここでは何もしない（もう確定済みだから）
        if ($methodMeta === 2 && $session->payment_status !== 'paid') {
            return redirect()->route('items.index')->with('error', '決済を確認できませんでした。');
        }
        if ($methodMeta === 1) {
            // 既に redirectToCheckout() で確定済み。
            return redirect()->route('items.index')->with('message', '購入が完了しました。');
        }

        if ($session->payment_status === 'paid') {
            $itemId  = (int)($pi->metadata->item_id ?? 0);
            $buyerId = (int)($pi->metadata->buyer_id ?? 0);
            $method  = (int)($pi->metadata->payment_method ?? 2);
            $method  = 2;

            // 配送先：ドラフト優先→プロフィール補完
            $draft   = session("draft_address.{$itemId}", []);
            $profile = auth()->user()?->profile;
            $base    = [
                'postal_code' => $profile?->postal_code,
                'address'     => $profile?->address,
                'building'    => $profile?->building,
            ];
            $shipping = array_replace($base, array_intersect_key($draft, $base));

            DB::transaction(function () use ($itemId, $buyerId, $method, $shipping) {
                $item = Item::whereKey($itemId)->lockForUpdate()->firstOrFail();

                if ($item->seller_id === $buyerId)  throw new \RuntimeException('自分の商品は購入できません。');
                if ($item->is_sold)                 throw new \RuntimeException('この商品はすでに売り切れです。');

                Transaction::create([
                    'item_id'              => $item->id,
                    'buyer_id'             => $buyerId,
                    'purchase_price'       => $item->price,
                    'payment_method'       => $method,
                    'shipping_postal_code' => $shipping['postal_code'] ?? null,
                    'shipping_address'     => $shipping['address'] ?? null,
                    'shipping_building'    => $shipping['building'] ?? null,
                    'is_paid'            => true,
                ]);

                $item->update(['is_sold' => true]);
            });

            session()->forget("draft_address.{$itemId}");
            return redirect()->route('items.index')->with('message', '購入が完了しました。');
        }
        return redirect()->route('items.index')->with('error', '決済を確認できませんでした。');
    }
}
