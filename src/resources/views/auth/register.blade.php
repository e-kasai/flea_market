@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
@endpush

@section("content")
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
            <button class="btn" type="submit">登録する</button>
            <a class="link" href="{{ route("login") }}">ログインはこちら</a>
        </x-slot>
        {{-- ここまでが$slotに入る --}}
    </x-form.card>
@endsection
