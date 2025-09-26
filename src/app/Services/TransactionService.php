<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function completePurchase(Item $item, array $shippingAddress, int $paymentMethod): void
    {
        DB::transaction(function () use ($item, $shippingAddress, $paymentMethod) {
            $locked = Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
            if ($locked->is_sold) {
                throw new \RuntimeException('この商品はすでに売り切れです。');
            }
            if ($locked->seller_id === auth()->id()) {
                throw new \RuntimeException('自分の商品は購入できません。');
            }

            $buyer = auth()->user();
            $buyer->transactions()->create([
                'item_id' => $locked->id,
                'purchase_price' => $locked->price,
                'payment_method' => $paymentMethod,
                'shipping_postal_code' => $shippingAddress['postal_code'] ?? null,
                'shipping_address' => $shippingAddress['address'] ?? null,
                'shipping_building' => $shippingAddress['building'] ?? null,
                'is_paid' => true,
            ]);
            $locked->update(['is_sold' => true]);
            session()->forget("draft_address.{$item->id}");
        });
    }
}
