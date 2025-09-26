<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_login_fails_if_email_is_empty()
    {
        //メールアドレスを入れずにログイン
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password123',
        ]);

        //期待：メールアドレスを入力してくださいというエラーメッセージが返る
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_login_fails_if_password_is_empty()
    {
        //パスワードを入れずにログイン
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        //期待：パスワードを入力してくださいというエラーメッセージが返る
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }


    public function test_login_fails_if_email_is_not_registered()
    {
        $response = $this->post(route('login'), [
            //メールアドレスは未登録想定（RefreshDatabaseしてるのでそもそも登録済みアドレスがないため）
            'email' => 'no-such-user@example.com',
            'password' => 'password',
        ]);

        //期待：ログイン情報が登録されていませんというエラーメッセージが返る
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function test_login_fails_if_password_is_incorrect()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
        //期待：ログイン情報が登録されていませんというエラーメッセージが返る
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function test_login_succeeds_with_valid_credentials()
    {
        //ユーザーを作成（ハッシュ済みパスワード）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        //作成した正しい資格情報でログイン
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        //期待1: 認証されたことが確認できる
        $this->assertAuthenticatedAs($user);

        //期待2: ログイン時のリダイレクト先商品一覧に遷移する
        $response->assertRedirect(route('items.index'));
    }
}
