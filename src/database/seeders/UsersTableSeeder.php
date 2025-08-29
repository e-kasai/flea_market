<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin_user@example.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), //管理者はメール認証済みとし開発効率を上げる
            ]
        );
        User::firstOrCreate(
            ['email' => 'general_user@example.com'],
            [
                'name' => '一般ユーザー',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name' => 'ユーザー1',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => '一般ユーザー2',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user3@example.com'],
            [
                'name' => '一般ユーザー3',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user4@example.com'],
            [
                'name' => '一般ユーザー4',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user5@example.com'],
            [
                'name' => '一般ユーザー5',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user6@example.com'],
            [
                'name' => '一般ユーザー6',
                'password' => Hash::make('password'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'user7@example.com'],
            [
                'name' => '一般ユーザー7',
                'password' => Hash::make('password'),
            ]
        );
    }
}
