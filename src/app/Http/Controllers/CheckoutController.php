<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Profile;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;

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
            $this->completePurchase($item, $shippingAddress, self::METHOD_KONBINI);
            session()->forget("draft_address.{$item->id}");
        }

        $paymentMethodTypes = ($paymentMethod === self::METHOD_KONBINI)
            ? ['konbini']
            : ['card'];

        $successUrl = ($paymentMethod === self::METHOD_CARD)
            ? route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}'
            : route('items.index', [], true);

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => $paymentMethodTypes,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->item_name],
                    'unit_amount' => (int) $item->price,
                ],
                'quantity' => 1,
            ]],

            'success_url' => $successUrl,
            'cancel_url'  => route('details.show', $item, true),

            'custom_text' => [
                'after_submit' => [
                    'message' => "コンビニ払いは現時点で購入完了仕様です。左上の ← を押して戻ってください。",
                ],
            ],

            'payment_intent_data' => [
                'metadata' => [
                    'item_id'        => (string) $item->id,
                    'buyer_id'       => (string) auth()->id(),
                    'payment_method' => (string) $paymentMethod,
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
        $paymentMethod = (int) ($paymentIntent->metadata->payment_method ?? self::METHOD_CARD);
        $itemId = (int) ($paymentIntent->metadata->item_id ?? 0);
        $item   = Item::findOrFail($itemId);
        $profile = auth()->user()?->profile;
        $shippingAddress = $this->buildShippingAddress($item, $profile);

        //コンビニ
        if ($paymentMethod === self::METHOD_KONBINI) {
            return redirect()->route('items.index')
                ->with('message', '購入が完了しました。');
        }


        //カード
        if ($paymentMethod === self::METHOD_CARD && $session->payment_status !== 'paid') {
            return redirect()->route('items.index')->with('error', '決済を確認できませんでした。');
        }
        $this->completePurchase($item, $shippingAddress, self::METHOD_CARD);
        session()->forget("draft_address.{$itemId}");
        return redirect()->route('items.index')->with('message', '購入が完了しました。');
    }


    // プライベートメソッド
    private function buildShippingAddress(Item $item, ?Profile $profile): array
    {
        $originalAddress = [
            'postal_code' => $profile?->postal_code,
            'address'     => $profile?->address,
            'building'    => $profile?->building,
        ];
        $draftAddress = session("draft_address.{$item->id}", []);
        $draftAddress = array_intersect_key($draftAddress, $originalAddress);

        return array_replace($originalAddress, $draftAddress);
    }

    private function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    private function completePurchase(Item $item, array $shippingAddress, int $paymentMethod): void
    {
        DB::transaction(function () use ($item, $shippingAddress, $paymentMethod) {
            $locked = Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
            if ($locked->is_sold) {
                throw new \RuntimeException('この商品はすでに売り切れです。');
            }

            $buyer = auth()->user();

            $buyer->transactions()->create([
                'item_id'              => $locked->id,
                'purchase_price'       => $locked->price,
                'payment_method'       => $paymentMethod,
                'shipping_postal_code' => $shippingAddress['postal_code'] ?? null,
                'shipping_address'     => $shippingAddress['address'] ?? null,
                'shipping_building'    => $shippingAddress['building'] ?? null,
                'is_paid'              => true,
            ]);
            $locked->update(['is_sold' => true]);
        });
    }
}
