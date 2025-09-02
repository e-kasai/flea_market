<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'name'              => 'admin',
                'email'             => 'admin_user@example.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => $now,   // 管理者は認証済み
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'       => 'general',
                'email'      => 'general_user@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => $now,   // 一般ユーザーは認証済み
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user3',
                'email'      => 'user3@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user4',
                'email'      => 'user4@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user5',
                'email'      => 'user5@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user6',
                'email'      => 'user6@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user7',
                'email'      => 'user7@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user8',
                'email'      => 'user8@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user9',
                'email'      => 'user9@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'user10',
                'email'      => 'user10@example.com',
                'password'   => Hash::make('password'),
                'email_verified_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('users')->insert($users);
    }
}
