<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\CategoriesTableSeeder;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_shows_product_image()
    {
        $item = Item::factory()->create([
            'image_path' => 'https://example.com/test.jpg',
        ]);

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        $response->assertSee('src="https://example.com/test.jpg"', false);
    }

    public function test_item_detail_shows_product_name_and_brand_name()
    {
        $item = Item::factory()->create([
            'item_name' => 'VAIO',
            'brand_name' => 'SONY'
        ]);

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        $response->assertSeeText('VAIO');
        $response->assertSeeText('SONY');
    }

    public function test_item_detail_shows_product_price_and_description()
    {
        $item = Item::factory()->create([
            'price' => '10000',
            'description' => '動作確認済み'
        ]);

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        //number_formatがあるので￥をいれてテスト
        $response->assertSeeText('¥10,000');
        //デザイン上記載されてるため念のため税込もテスト
        $response->assertSeeText('(税込)');
        $response->assertSeeText('動作確認済み');
    }

    public function test_item_detail_shows_favorites_count()
    {
        $item = Item::factory()->create();
        $users = User::factory()->count(3)->create();

        $item->favoritedByUsers()->attach($users->pluck('id')->all());

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        $response->assertSeeText('3');
    }

    public function test_item_detail_shows_comments_count()
    {
        $item = Item::factory()->create();
        Comment::factory()->count(2)->create([
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        $response->assertSeeText('2');
    }

    public function test_item_detail_shows_comment_list_with_user_name_and_body()
    {
        $item = Item::factory()->create();
        $zhang = User::factory()->create(['name' => 'Zhang']);
        $bob = User::factory()->create(['name' => 'Bob']);

        Comment::factory()->create(
            [
                'item_id' => $item->id,
                'user_id' => $zhang->id,
                'body'    => '１５日以降で配送可能ですか？',
            ]
        );

        Comment::factory()->create(
            [
                'item_id' => $item->id,
                'user_id' => $bob->id,
                'body'    => '購入希望です',
            ]
        );

        $response = $this->get(route('details.show', $item));
        $response->assertOk();

        //レスポンスのHTML全体を文字列として取り出す
        $html = $response->getContent();
        //正規表現にマッチする文字列が $html にあるかどうか = 誰がどのコメントかを含めチェックする
        $this->assertMatchesRegularExpression('/Zhang.*１５日以降で配送可能ですか？/s', $html);
        $this->assertMatchesRegularExpression('/Bob.*購入希望です/s', $html);
    }

    public function test_item_detail_shows_multiple_category_names()
    {
        $this->seed(CategoriesTableSeeder::class);

        $item = Item::factory()->create();
        $categoryA = Category::where('category_name', '家電')->firstOrFail();
        $categoryB = Category::where('category_name', 'キッチン')->firstOrFail();

        $item->categories()->attach([$categoryA->id, $categoryB->id]);

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        $response->assertSeeText('家電');
        $response->assertSeeText('キッチン');
    }

    public function test_item_detail_shows_condition_label()
    {
        $item = \App\Models\Item::factory()->create([
            'condition' => 2,
        ]);

        $response = $this->get(route('details.show', $item));
        $response->assertOk();
        $response->assertSeeText('目立った傷や汚れなし');
    }
}
