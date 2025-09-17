<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\CategoriesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExhibitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_user_can_exhibit_item()
    {
        $seller = User::factory()->create();
        $this->actingAs($seller);
        $this->seed(CategoriesTableSeeder::class);

        //先頭２件のカテゴリを使う
        $categoryIds = Category::pluck('id')->take(2)->all();
        //ダミー画像
        Storage::fake('public');
        $file = UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');

        $response = $this->post(route('exhibit.store'), [
            'seller_id' => $seller->id,
            'item_name' => '出品商品',
            'price'     => 1000,
            'is_sold'   => 0,
            'image_path' => $file,
            'brand_name'     => 'ACompany',
            'condition' => 1,
            'description' => '使用期間１年',
            'category_ids' => $categoryIds,
        ]);

        $response->assertSessionHasNoErrors();

        // 作成されたItemを取得
        $item = Item::where('seller_id', $seller->id)
            ->where('item_name', '出品商品')
            ->firstOrFail();

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'id'         => $item->id,
            'seller_id' => $seller->id,
            'item_name' => '出品商品',
            'price'     => 1000,
            'is_sold'   => 0,
            'brand_name'     => 'ACompany',
            'condition' => 1,
            'description' => '使用期間１年',
        ]);

        //画像はハッシュ化されるので部分一致でDBカラムへのセットを確認
        $this->assertStringStartsWith('items/', $item->image_path);
        //実際にファイルが保存されたかの確認
        Storage::disk('public')->assertExists($item->image_path);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_item', [
                'item_id'     => $item->id,
                'category_id' => $categoryId,
            ]);
        }
    }
}
