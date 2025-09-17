<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_selected_and_preview_label_match(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $response = $this
            ->withSession(['_old_input' => ['payment_method' => '2']])
            ->get(route('purchase.show', $item));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertMatchesRegularExpression(
            '#<option[^>]*value="2"[^>]*selected[^>]*>\s*カード支払い\s*</option>#s',
            $html
        );
        $this->assertMatchesRegularExpression(
            '#<p[^>]*id="payment_preview"[^>]*>\s*カード支払い\s*</p>#u',
            $html
        );
    }

    public function test_conbini_selected_and_preview_label_match(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $response = $this
            ->withSession(['_old_input' => ['payment_method' => '1']])
            ->get(route('purchase.show', $item));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertMatchesRegularExpression(
            '#<option[^>]*value="1"[^>]*selected[^>]*>\s*コンビニ払い\s*</option>#s',
            $html
        );
        $this->assertMatchesRegularExpression(
            // '#<p[^>]*data-testid="payment_preview"[^>]*>\s*コンビニ払い\s*</p>#u',
            '#<p[^>]*id="payment_preview"[^>]*>\s*コンビニ払い\s*</p>#u',
            $html
        );
    }
}
