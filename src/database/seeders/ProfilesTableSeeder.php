<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    public function run(): void
    {
        // usersテーブルの全IDを抜き出す
        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $id) {
            DB::table('profiles')->updateOrInsert(
                ['user_id' => $id],
                [
                    'postal_code' => '100000' . str_pad($id, 1, '0', STR_PAD_LEFT), // 末尾が$idになる
                    'address'     => '東京都千代田区' . $id . '番地',
                    'building'    => 'テストビル' . $id,
                    'avatar_path' => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
