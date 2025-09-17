<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UpdateUserInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_shows_avatar_username_sold_and_purchased_lists()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー1',
            'email_verified_at' => now(),
        ]);

        $user->profile()->create([
            'postal_code' => '100-0001',
            'address'     => 'テスト区1',
            'avatar_path' => 'avatars/sample1.png',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertSee('value="テストユーザー1"', false);
        $response->assertSee('/storage/avatars/sample1.png');
        $response->assertSee('value="100-0001"', false);
        $response->assertSee('value="テスト区1"', false);
    }
}
