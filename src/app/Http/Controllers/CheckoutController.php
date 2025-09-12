<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function redirectToCheckout(Request $request, Item $item)
    {

        $stripe = new StripeClient(config('services.stripe.secret'));

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
            'success_url' => route('details.show', $item), // 支払い完了した人用の出口
            'cancel_url'  => route('details.show', $item), // やっぱやめた人用の出口 これがないとstripe画面から出られない
        ]);

        // urlはstripeが返すレスポンスの中にあるキー

        return redirect($session->url);
    }
}
