<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $fixedProfiles = [
            1 => ['postal' => '100-0001', 'address' => '東京都千代田区1丁目', 'building' => '東京タワー101'],
            2 => ['postal' => '100-0002', 'address' => '東京都千代田区2丁目', 'building' => '東京タワー102'],
            3 => ['postal' => '100-0003', 'address' => '東京都千代田区3丁目', 'building' => '東京タワー103'],
            4 => ['postal' => '100-0004', 'address' => '東京都千代田区4丁目', 'building' => '東京タワー104'],
            5 => ['postal' => '100-0005', 'address' => '東京都千代田区5丁目', 'building' => '東京タワー105'],
            6 => ['postal' => '100-0006', 'address' => '東京都千代田区6丁目', 'building' => '東京タワー106'],
            7 => ['postal' => '100-0007', 'address' => '東京都千代田区7丁目', 'building' => '東京タワー107'],
            8 => ['postal' => '100-0008', 'address' => '東京都千代田区8丁目', 'building' => '東京タワー108'],
            9 => ['postal' => '100-0009', 'address' => '東京都千代田区9丁目', 'building' => '東京タワー109'],
            10 => ['postal' => '100-0010', 'address' => '東京都千代田区10丁目', 'building' => '東京タワー110'],
        ];

        // usersテーブルの全IDを抜き出す
        $ids = DB::table('users')->orderBy('id')->pluck('id');

        $profiles = $ids->map(
            function ($userId) use ($fixedProfiles, $now) {
                $data = $fixedProfiles[$userId] ??
                    [
                        'postal' => '000-0000',
                        'address' => '東京都千代田区0丁目',
                        'building' => '',
                    ];

                return [
                    'user_id'     => $userId,
                    'postal_code' => $data['postal'],
                    'address'     => $data['address'],
                    'building'    => $data['building'],
                    'avatar_path' => null,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        )->all();

        DB::table('profiles')->insert($profiles);
    }
}
