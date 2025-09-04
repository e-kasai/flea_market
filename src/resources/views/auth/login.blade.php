@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
@endpush

@section("content")
    <x-form.card title="ログイン" action="{{ route('login') }}" method="POST">
        {{-- ----------------------------------- --}}
        {{-- ここから下がcard.blade.phpの$slotに入る --}}
        {{-- メール --}}
        <x-form.input type="email" name="email" label="メールアドレス" value="{{ old('email') }}" required />
        {{-- パスワード --}}
        <x-form.input type="password" name="password" label="パスワード" required />
        {{-- ボタン --}}
        <x-slot name="actions">
            <button class="btn" type="submit">ログインする</button>
            <a class="link" href="{{ route("login") }}">会員登録はこちら</a>
        </x-slot>
        {{-- ここまでが$slotに入る --}}
    </x-form.card>
@endsection
