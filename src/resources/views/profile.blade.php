@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/profile.css") }}" />
@endpush

@section("content")
    <section class="profile">
        {{-- プロフィール画像 --}}
        <img
            class="avatar"
            src="{{
                $userProfile->avatar_path
                    ? asset("storage/" . $userProfile->avatar_path)
                    : asset("img/dog.jpg")
            }}"
            alt="プロフィール画像"
        />
        {{-- ユーザー名 --}}
        <h1 class="profile__name">{{ $currentUser->name }}</h1>
        {{-- プロフィールを編集 --}}
        <a class="profile__link-edit" href="{{ route("profile.edit") }}">プロフィールを編集</a>
    </section>
    <section class="tabs">
        {{-- タブで出品、購入一覧を切り替え --}}
        <div class="tabs__switch">
            <label class="tabs__label">
                <input type="radio" name="TAB" checked />
                出品した商品
            </label>
            <div class="tabs__content">まだ出品データがありません</div>
            {{-- 購入一覧 --}}
            <label class="tabs__label">
                <input type="radio" name="TAB" />
                購入した商品
            </label>
            <div class="tabs__panel">まだ購入データがありません</div>
        </div>
    </section>
@endsection
