<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Models\Item;

class PaymentService
{
    // Stripe クライアントを初期化
    public function __construct(private StripeClient $stripe)
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    //Stripeに保存されているCheckoutセッションを取り出す
    public function retrieveSession(string $sessionId)
    {
        return $this->stripe->checkout->sessions->retrieve(
            $sessionId,
            ['expand' => ['payment_intent']]
        );
    }

    //StripeのCheckoutページを作成する
    public function createCheckoutSession(Item $item, int $method, string $successUrl, string $cancelUrl): string
    {
        $paymentMethod = $method === 1 ? ['konbini'] : ['card'];

        $session = $this->stripe->checkout->sessions->create([
            'mode'                 => 'payment',
            'payment_method_types' => $paymentMethod,
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'product_data' => ['name' => $item->item_name],
                    'unit_amount'  => (int) $item->price,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'submit_type' => 'pay',
            'custom_text' => [
                'after_submit' => [
                    'message' => "コンビニ払いは購入完了済みです。\n左上の ← で戻ってください。",
                ],
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'item_id'  => (string) $item->id,
                    'buyer_id' => (string) auth()->id(),
                    'payment_method'   => (string) $method,
                ],
            ],
        ]);
        return $session->url;
    }
}
