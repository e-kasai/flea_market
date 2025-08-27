<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 採点に必要なログイン用アカウント（管理者、一般）
        User::firstOrCreate(
            ['email' => 'admin_user@example.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), //管理者はメール認証済としておき開発効率を上げる
            ]
        );

        User::firstOrCreate(
            ['email' => 'general_user@example.com'],
            [
                'name' => '一般ユーザー',
                'password' => Hash::make('password'),
            ]
        );

        User::factory()->count(20)->create();
    }
}
