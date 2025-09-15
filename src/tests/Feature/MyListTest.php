<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    //テスト:mylistでいいねした商品だけが表示される
    public function test_mylist_shows_only_items_i_favorited()
    {
        // ユーザーと商品を準備
        $user   = User::factory()->create();
        $liked  = Item::factory()->create(['item_name' => 'いいねした商品']);
        $other  = Item::factory()->create(['item_name' => 'いいねしてない商品']);

        // ユーザーが liked にだけいいね
        $user->favoriteItems()->attach($liked->id);

        // ログインして mylist を開く
        $response = $this->actingAs($user)->get(
            route('items.index', ['tab' => 'mylist'])
        );

        //期待1:マイリストにアクセスできる
        $response->assertOk();
        //期待2:マイリストにいいねした商品が表示される
        $response->assertSeeText('いいねした商品');
        //期待3:マイリストにいいねしてない商品は表示されない
        $response->assertDontSee('いいねしてない商品');
    }

    //テスト:mylistで購入済み商品はsoldと表示される
    public function test_sold_item_indicate_sold_in_my_page()
    {

        $user   = User::factory()->create();
        // 売り切れ商品を１件作成
        $item = Item::factory()->create([
            'item_name'  => '売り切れ商品',
            'image_path' => 'https://example.com/test.jpg',
            'is_sold'   => true,
        ]);
        // ユーザーがこの商品をいいね
        $user->favoriteItems()->attach($item->id);
        // ログインして mylist を開く
        $response = $this->actingAs($user)->get(
            route('items.index', ['tab' => 'mylist'])
        );

        //期待1:マイリストにアクセスできる
        $response->assertOk();
        // 期待2:売り切れ商品にSOLD表示がある
        $response->assertSeeText('売り切れ商品');
        $response->assertSeeText('SOLD');
    }

    //テスト:未認証の場合はmylistに何も表示されない
    public function test_guest_user_sees_nothing_in_mylist()
    {
        //商品を1件だけ用意
        $item = Item::factory()->create([
            'item_name' => 'favorited',
        ]);

        //ゲストでいいねしセッションを保持したままマイリストへアクセス
        $response = $this->withSession([
            'guestFavorited' => [$item->id],
        ])->get(route('items.index', ['tab' => 'mylist']));

        //期待:マイリストにアクセスできるが商品名は何も表示されない
        $response->assertOk();
        $response->assertDontSeeText('favorited');
    }
}
