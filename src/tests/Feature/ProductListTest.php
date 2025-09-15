<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp(); // 親の準備処理
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_all_products_can_be_acquired()
    {
        // 商品を5件作成
        $items = Item::factory()->count(5)->create([
            'image_path' => 'https://example.com/test.jpg',
            'is_sold'   => false,
        ]);
        // 商品一覧ページを表示
        $response = $this->get(route('items.index'));

        // 期待1: 商品一覧ページにアクセスが成功する
        $response->assertOk();

        // 期待2: viewの件数が5件
        $response->assertViewHas('items', function ($product) {
            return $product->count() === 5;
        });

        // 期待3: 全商品の名称が画面に出ている
        foreach ($items as $item) {
            $response->assertSee($item->item_name, false);
        }
    }


    //商品一覧ページで売り切れた商品にsoldが表示されるかのテスト
    public function test_sold_item_indicate_sold_in_top_page()
    {
        // 売り切れ商品を１件作成
        $item = Item::factory()->create([
            'item_name'  => '売り切れ商品',
            'image_path' => 'https://example.com/test.jpg',
            'is_sold'   => true,
        ]);
        // 商品一覧ページを表示
        $response = $this->get(route('items.index'));

        //期待1: 一覧ページが返る
        $response->assertOk();
        // 期待2: 売り切れ商品にSOLD表示がある
        $response->assertSee('売り切れ商品');
        $response->assertSeeText('SOLD');
    }


    //自分が出品した商品は一覧に表示されないをテスト
    public function test_my_exhibited_items_are_hidden_from_top_page()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id'  => $seller->id,
            'image_path' => 'https://example.com/test.jpg',
            'is_sold'    => false,
            'item_name'  => 'myitem',
        ]);

        $othersItem = Item::factory()->create([
            'item_name'  => 'othersitem',
            'image_path' => 'https://example.com/test.jpg',
            'is_sold'    => false,
        ]);

        $this->actingAs($seller);

        // 期待1: 認証されたことが確認できる
        $this->assertAuthenticatedAs($seller);

        // 一覧ページを表示
        $response = $this->actingAs($seller)->get(route('items.index'));

        //期待1:ログインした状態で一覧ページが返る
        $response->assertOk();
        //期待2:自分が出品した商品が一覧に表示されない
        $response->assertDontSeeText($item->item_name);
        //期待3:他人が出品した商品は一覧に表示される
        $response->assertSeeText($othersItem->item_name);
    }
}
