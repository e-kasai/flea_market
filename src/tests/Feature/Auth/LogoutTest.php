<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_logout_enable()
    {
        // ファクトリでユーザーを作ってログインする
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        //ログアウトボタンを押す
        $response = $this->post(route('logout'));

        // 期待1：未ログイン状態になる
        $this->assertGuest();

        // 期待2：ログアウト後は商品ページ一覧にリダイレクトする
        $response->assertRedirect(route('items.index'));
    }
}
