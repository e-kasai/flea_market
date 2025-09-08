@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/profile.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/tab.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/grid.css") }}" />
@endpush

@section("content")
    <section class="profile">
        {{-- プロフィール画像 --}}
        <div class="profile__info">
            <img
                class="avatar"
                src="{{
                    $profile->avatar_path
                        ? asset("storage/" . $profile->avatar_path)
                        : asset("img/noimage.png")
                }}"
                alt="プロフィール画像"
            />
            {{-- ユーザー名 --}}
            <h1 class="profile__name">{{ $user->name }}</h1>
        </div>
        <div class="profile__link">
            {{-- プロフィールを編集 --}}
            <a class="profile__link-edit" href="{{ route("profile.edit") }}">プロフィールを編集</a>
        </div>
    </section>

    <nav class="tabs">
        @php
            $activeTab = request("page", "sell");
            $tabs = [
                ["label" => "出品した商品", "href" => route("profile.show", ["page" => "sell"]), "active" => $activeTab === "sell" ? "is-active" : ""],
                ["label" => "購入した商品", "href" => route("profile.show", ["page" => "buy"]), "active" => $activeTab === "buy"],
            ];
        @endphp

        <x-tabs.nav :items="$tabs" />
    </nav>

    {{-- 出品一覧（共通グリッド） --}}
    @if ($activeTab === "sell")
        <section class="tabs__content">
            <x-grid.item :items="$items" />
        </section>
    @endif

    {{-- 購入一覧（共通グリッド） --}}
    @if ($activeTab === "buy")
        <div class="tabs__content">
            <x-grid.item :items="$purchasedItems" />
        </div>
    @endif
@endsection
