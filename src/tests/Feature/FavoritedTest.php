<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoritedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_logged_in_user_can_favorite_an_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $this->assertDatabaseCount('favorites', 0);

        $response = $this->post(route('favorite.store', $item));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('details.show', $item));
        //viewの別要素の1を検出してしまう可能性がある為正規表現でテスト
        $this->assertMatchesRegularExpression(
            '/<span\s+class="count">\s*1\s*<\/span>/',
            $response->getContent()
        );
    }

    public function test_changed_favorite_icon_color_correctly_if_it_favorited()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create();

        $before = $this->get(route('details.show', $item));
        $before->assertSee('aria-pressed="false"', false);
        $before->assertSee('<i class="fa-regular fa-star"></i>', false);

        $after = $this->post(route('favorite.store', $item));
        $after = $this->get(route('details.show', $item));
        $after->assertSee('aria-pressed="true"', false);
        $after->assertSee('<i class="fa-solid fa-star"></i>', false);
    }

    public function test_logged_in_user_can_unfavorite_an_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $this->assertDatabaseCount('favorites', 0);

        //いいねする
        $response = $this->post(route('favorite.store', $item));
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->get(route('details.show', $item));
        $this->assertMatchesRegularExpression(
            '/<span\s+class="count">\s*1\s*<\/span>/',
            $response->getContent()
        );
        //いいねを解除する
        $response = $this->delete(route('favorite.destroy', $item));
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->get(route('details.show', $item));
        $this->assertMatchesRegularExpression(
            '/<span\s+class="count">\s*0\s*<\/span>/',
            $response->getContent()
        );
    }
}
