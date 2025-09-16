<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }


    public function test_selected_payment_method_is_reflected_on_summary_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('purchase.item'), [
            'payment_method' => '2',
        ]);

        $this->assertDatabaseHas('transactions', [
            'payment_method'  => 2,
        ]);

        $response->assertOk();
        $response->assertSeeText('カード支払い');
    }
}
