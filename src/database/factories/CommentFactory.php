<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;


class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $dummyComments = [
            '値下げ可能ですか？',
            '沖縄ですが配送可能ですか？',
            '購入時期を教えてください',
            'サイズは大体どのくらいですか？',
            '明日購入したいので専用にしてもらえますか',
        ];


        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'body' => $this->faker->randomElement($dummyComments),
        ];
    }
}
