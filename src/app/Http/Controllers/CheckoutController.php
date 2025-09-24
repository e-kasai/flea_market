<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Profile;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class CheckoutController extends Controller
{
    private const METHOD_KONBINI = 1;
    private const METHOD_CARD    = 2;

    public function startPayment(Request $request, Item $item)
    {
        $stripe = $this->stripe();
        $user   = auth()->user();
        $paymentMethod = (int) ($request->input('payment_method'));

        // コンビニ = ここで即確定（SOLD & Transaction作成）
        if ($paymentMethod === self::METHOD_KONBINI) {
            $profile = $user->profile;
            $shippingAddress = $this->buildShippingAddress($item, $profile);
            $this->completePurchase($item, $user->id, $shippingAddress, self::METHOD_KONBINI);

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

            'success_url' => route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('details.show', $item, true),

            // 完了時に特定できるよう metadata を付与
            'payment_intent_data' => [
                'metadata' => [
                    'item_id'        => (string) $item->id,
                    'buyer_id'       => (string) auth()->id(),
                    'payment_method' => (string) ($request->input('payment_method') ?? self::METHOD_CARD),
                ],
            ],
        ]);
        return redirect($session->url);
    }


    public function finalizeTransaction(Request $request)
    {
        $sessionId = $request->query('session_id');
        abort_if(!$sessionId, 400, 'session_id missing');

        $stripe = $this->stripe();
        $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['payment_intent']]);

        $paymentIntent = $session->payment_intent;
        $paymentMethod = (int)($paymentIntent->metadata->payment_method);

        //カード
        if ($paymentMethod === self::METHOD_CARD && $session->payment_status !== 'paid') {
            return redirect()->route('items.index')->with('error', '決済を確認できませんでした。');
        }
        //コンビニは何もしない（もう確定済み）
        if ($paymentMethod === self::METHOD_KONBINI) {
            return redirect()->route('items.index')->with('message', '購入が完了しました。');
        }

        if ($session->payment_status === 'paid') {
            $itemId  = (int)($paymentIntent->metadata->item_id ?? 0);
            $buyerId = (int)($paymentIntent->metadata->buyer_id ?? 0);
            $paymentMethod  = self::METHOD_CARD;

            $item = Item::findOrFail($itemId);

            $profile = auth()->user()?->profile;
            $shippingAddress = $this->buildShippingAddress($item, $profile);
            $this->completePurchase($item, $buyerId, $shippingAddress, self::METHOD_CARD);

            session()->forget("draft_address.{$itemId}");
            return redirect()->route('items.index')->with('message', '購入が完了しました。');
        }
        return redirect()->route('items.index')->with('error', '決済を確認できませんでした。');
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

    private function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    private function completePurchase(Item $item, int $buyerId, array $shippingAddress, int $paymentMethod): void
    {
        DB::transaction(function () use ($item, $buyerId, $shippingAddress, $paymentMethod) {
            $locked = Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
            if ($locked->is_sold) {
                throw new \RuntimeException('この商品はすでに売り切れです。');
            }
            Transaction::create([
                'item_id'              => $locked->id,
                'buyer_id'             => $buyerId,
                'purchase_price'       => $locked->price,
                'payment_method'       => $paymentMethod,
                'shipping_postal_code' => $shippingAddress['postal_code'] ?? null,
                'shipping_address'     => $shippingAddress['address'] ?? null,
                'shipping_building'    => $shippingAddress['building'] ?? null,
            ]);
            $locked->update(['is_sold' => true]);
        });
    }
}
