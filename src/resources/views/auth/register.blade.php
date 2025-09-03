<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>COACHTECH</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/register.css") }}" />
    </head>

    <body>
        <header>
            @include("layouts.header")
        </header>

        <main>
            {{-- 共通レイアウトに差し込む部分 --}}
            <x-form.card title="会員登録" action="{{ route('register.store') }}" method="POST">
                {{-- ----------------------------------- --}}
                {{-- ここから下がcard.blade.phpの$slotに入る --}}
                {{-- 名前 --}}
                <x-form.input type="text" name="name" label="ユーザー名" value="{{ old('name') }}" required />
                {{-- メール --}}
                <x-form.input type="email" name="email" label="メールアドレス" value="{{ old('email') }}" required />
                {{-- パスワード --}}
                <x-form.input type="password" name="password" label="パスワード" required />
                {{-- 確認用パスワード --}}
                <x-form.input type="password" name="password_confirmation" label="確認用パスワード" required />
                {{-- ボタン --}}
                <x-slot name="actions">
                    <button type="submit">登録する</button>
                </x-slot>
                {{-- ここまでが$slotに入る --}}
            </x-form.card>
            {{-- ここまで --}}
            <p class="login-link"><a href="{{ route("login") }}">ログインはこちら</a></p>
        </main>
    </body>
</html>
