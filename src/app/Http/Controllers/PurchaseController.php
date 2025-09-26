<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingAddressRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\TransactionService;

class PurchaseController extends Controller
{
    public function __construct(
        private PaymentService $payment,
        private TransactionService $transaction
    ) {}

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
        return $this->startPayment($request, $item);
    }

    private const METHOD_KONBINI = 1;
    private const METHOD_CARD    = 2;

    public function startPayment(Request $request, Item $item)
    {
        $user   = auth()->user();
        $paymentMethod = (int) ($request->input('payment_method'));

        // コンビニ = ここで即確定（SOLD & Transaction作成）
        if ($paymentMethod === self::METHOD_KONBINI) {
            $profile = $user->profile;
            $shippingAddress = $this->buildShippingAddress($item, $profile);
            $this->transaction->completePurchase($item, $shippingAddress, self::METHOD_KONBINI);
        }

        $successUrl = route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = route('details.show', $item, true);

        $url = $this->payment->createCheckoutSession(
            $item,
            $paymentMethod,
            $successUrl,
            $cancelUrl
        );
        return redirect($url);
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


    public function finalizeTransaction(Request $request)
    {
        $sessionId = $request->query('session_id');
        abort_if(!$sessionId, 400, 'session_id missing');

        $session = $this->payment->retrieveSession($sessionId);

        $paymentIntent = $session->payment_intent;
        $paymentMethod = (int) ($paymentIntent->metadata->payment_method ?? self::METHOD_CARD);
        $itemId = (int) ($paymentIntent->metadata->item_id ?? 0);
        $item   = Item::findOrFail($itemId);
        $profile = auth()->user()?->profile;
        $shippingAddress = $this->buildShippingAddress($item, $profile);

        //カード
        if ($paymentMethod === self::METHOD_CARD && $session->payment_status !== 'paid') {
            return redirect()->route('items.index')->with('error', '決済を確認できませんでした。');
        }
        $this->transaction->completePurchase($item, $shippingAddress, self::METHOD_CARD);
        return redirect()->route('items.index')->with('message', '購入が完了しました。');
    }

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
}
