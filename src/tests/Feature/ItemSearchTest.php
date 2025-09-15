<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    //テスト:部分一致検索でヒットする（1件）
    public function test_search_shows_one_item_if_matched()
    {
        $item = Item::factory()->create([
            'item_name'  => 'iphone16',
        ]);

        $response = $this->get('/?keyword=iphone');
        $response->assertOk();
        $response->assertSeeText('iphone16');
    }

    //テスト:部分一致検索でヒットする（複数件）
    public function test_search_shows_multiple_items_if_matched()
    {
        Item::factory()->create(['item_name' => 'iPad Pro']);
        Item::factory()->create(['item_name' => 'MacBook Pro']);
        Item::factory()->create(['item_name' => 'Galaxy']);

        $response = $this->get('/?keyword=Pro');

        $response->assertOk();
        $response->assertSeeText('iPad Pro');
        $response->assertSeeText('MacBook Pro');
        $response->assertDontSeeText('Galaxy');
    }

    //テスト:検索に一致しない場合はヒットしない(0件)
    public function test_search_shows_no_results_if_none_matched()
    {
        Item::factory()->create(['item_name' => 'Dynabook']);

        $response = $this->get('/?keyword=PC');
        $response->assertOk();
        $response->assertDontSeeText('Dynabook');
    }

    // テスト:検索状態がマイリストでも保持されている
    public function test_keyword_persists_when_switching_to_mylist()
    {
        $user = User::factory()->create();

        $hitItem    = Item::factory()->create(['item_name' => '検索にヒットする商品']);
        $missItem   = Item::factory()->create(['item_name' => 'ヒットしない商品']);

        $user->favoriteItems()->attach([$hitItem->id, $missItem->id]);
        $this->get('/?keyword=検索')->assertOk();

        $response = $this->actingAs($user)->get(
            route('items.index', ['tab' => 'mylist', 'keyword' => '検索'])
        );

        // 期待：検索欄に value="検索"が残っている（エスケープ無効にしassertSee）
        $response->assertOk();
        $response->assertSee('value="検索"', false);
        $response->assertSeeText('検索にヒットする商品');
        $response->assertDontSeeText('ヒットしない商品');
    }
}
