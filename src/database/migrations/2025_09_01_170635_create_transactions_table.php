<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained()
                ->unique()
                ->onDelete('cascade');

            $table->foreignId('buyer_id')
                ->constrained('users')
                ->onDelete('cascade');

            // 支払い・金額関連
            $table->tinyInteger('payment_method');
            $table->unsignedInteger('purchase_price'); // JPYの整数
            // 'payment_status' = pendingにしておく（stripe導入後もmigration修正が不要）
            $table->tinyInteger('payment_status')->default(0); // 0=pending, 1=paid, 2=canceled

            // 支払い・キャンセル日時
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            // Stripe関連
            $table->string('payment_intent_id', 64)->nullable();
            $table->string('charge_id', 64)->nullable();

            // 配送先情報
            $table->char('shipping_postal_code', 8); // ハイフンあり8文字
            $table->string('shipping_address', 255);
            $table->string('shipping_building', 255)->nullable();

            // タイムスタンプ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}
