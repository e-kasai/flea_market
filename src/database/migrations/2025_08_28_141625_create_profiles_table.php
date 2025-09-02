<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{

    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            //uniqueを使い同じ user_id が複数行に重複しないようにする
            $table->foreignId('user_id')->constrained()->unique()->cascadeOnDelete();
            $table->char('postal_code', 8);
            $table->string('address', 255);
            $table->string('building', 255)->nullable();
            $table->string('avatar_path', 2048)->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
}

