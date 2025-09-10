<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 出品者（users.id）
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('item_name', 255);
            $table->string('brand_name', 255)->nullable();
            $table->unsignedInteger('price');
            $table->tinyInteger('condition');
            $table->string('description', 255);

            // 画像パス（storage配下の相対パス）
            $table->string('image_path', 2048);

            // soldフラグ,default = falseにしコントローラで購入時にtrueにする
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}
