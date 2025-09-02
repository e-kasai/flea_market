<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // usersテーブルの全IDを抜き出す
        $profiles = DB::table('users')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn($id) => [
                'user_id' => $id,
                //先頭は１、6桁になるまで0で埋める = 7桁になる
                'postal_code' => '1' . str_pad($id, 6, '0', STR_PAD_LEFT),
                'address'     => "東京都千代田区{$id}番地",
                'building'    => "テストビル{$id}",
                'avatar_path' => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ])
            ->all();

        DB::table('profiles')->insert($profiles);
    }
}
