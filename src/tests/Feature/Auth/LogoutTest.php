<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testLogoutEnable()
    {
        // ファクトリでユーザーを作ってログインする
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        //ログアウトボタンを押したを再現
        $response = $this->post(route('logout'));

        // 期待1：未ログイン状態になっている
        $this->assertGuest();

        // 期待2：ログアウト後は商品ページ一覧にリダイレクトする
        $response->assertRedirect('/');
    }
}
