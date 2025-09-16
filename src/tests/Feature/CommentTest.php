<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_logged_in_user_can_post_comment_and_count_increases()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $this->assertDatabaseCount('comments', 0);

        $response = $this->post(route('comments.store', $item), [
            'body' => 'テストコメント',
        ]);
        $response->assertRedirect(route('details.show', $item));

        $this->assertDatabaseHas('comments', [
            'user_id'  => $user->id,
            'item_id'  => $item->id,
            'body'  => 'テストコメント',
        ]);

        $show = $this->get(route('details.show', $item));
        $show->assertOk();
        $show->assertSeeText('テストコメント');

        $this->assertMatchesRegularExpression(
            '/<span\s+class="count">\s*1\s*<\/span>/',
            $show->getContent()
        );
    }

    public function test_guest_user_cannot_post_comment_and_is_redirected_to_login()
    {
        $item = Item::factory()->create();
        $this->assertDatabaseCount('comments', 0);

        $response = $this->post(route('comments.store', $item), [
            'body' => 'ゲストの投稿',
        ]);

        // 期待1：ログイン画面へリダイレクト（authミドルウェアの挙動）
        $response->assertRedirect(route('login'));

        // 期待2：DBに保存されていない
        $this->assertDatabaseCount('comments', 0);
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'body' => 'ゲストの投稿',
        ]);
    }

    public function test_empty_comment_is_rejected_with_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('comments.store', $item), [
            'body' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['body']);
        $this->assertDatabaseCount('comments', 0);
    }



    public function test_over_255_chars_comment_is_rejected_with_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $tooLong = str_repeat('あ', 256);

        $response = $this->post(route('comments.store', $item), [
            'body' => $tooLong,
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['body']);

        $this->assertDatabaseCount('comments', 0);
    }
}
