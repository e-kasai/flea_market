<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp(); // 親の準備処理
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_updated_shipping_address_reflects_on_purchase_page()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'postal_code' => '100-0000',
            'address'     => '東京都千代田区千代田1-1',
            'building'    => 'タワー101',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);
        $this->patch(
            route('address.update', $item),
            [
                'postal_code' => '100-0001',
                'address'     => 'テスト区1-2-3',
                'building'    => 'テストビル9F',
            ]
        );

        // 購入画面を再表示
        $response = $this->get(route('purchase.show', $item));
        $response->assertOk();

        // 新住所が画面に反映されていること
        $response->assertSeeText('100-0001');
        $response->assertSeeText('テスト区1-2-3');
        $response->assertSeeText('テストビル9F');
    }

    public function test_shipping_address_is_saved_with_transaction_on_purchase()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->profile()->create([
            'postal_code' => '100-0000',
            'address'     => '東京都千代田区千代田1-1',
            'building'    => 'タワー101',
        ]);

        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'is_sold'   => false,
        ]);

        $this->actingAs($user);
        $this->patch(
            route('address.update', $item),
            [
                'postal_code' => '100-0001',
                'address'     => 'テスト区1-2-3',
                'building'    => 'テストビル9F',
            ]
        )->assertStatus(302);

        // コンビニ払いでテスト → コンビニは購入ボタン押下 = 即Transaction作成仕様の為
        $this->post(route('purchase.item', $item), [
            'payment_method' => 1,
            'postal_code'    => '100-0001',
            'address'        => 'テスト区1-2-3',
            'building'       => 'テストビル9F',
        ])->assertStatus(302);

        // DBの購入済み商品に住所が紐づいて保存されている
        $this->assertDatabaseHas('transactions', [
            'item_id'              => $item->id,
            'buyer_id'             => $user->id,
            'payment_method'       => 1,
            'shipping_postal_code' => '100-0001',
            'shipping_address'     => 'テスト区1-2-3',
            'shipping_building'    => 'テストビル9F',
        ]);
    }
}
