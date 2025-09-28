@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/grid.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/tab.css") }}" />
@endpush

@section("content")
    @php
        $tabs = [
            ["label" => "おすすめ", "href" => request()->fullUrlWithQuery(["tab" => "recommend"]), "active" => $activeTab === "recommend" ? "is-active" : ""],
            ["label" => "マイリスト", "href" => request()->fullUrlWithQuery(["tab" => "mylist"]), "active" => $activeTab === "mylist"],
        ];
    @endphp

    <nav class="tabs">
        <x-tabs.nav :items="$tabs" />
    </nav>

    @if ($activeTab === "recommend")
        <section class="tabs__content">
            <div class="tabs__content">
                <x-grid.item :items="$items" />
            </div>
        </section>
    @endif

    @if ($activeTab === "mylist")
        @auth
            @if (auth()->user()->hasVerifiedEmail())
                <section class="tabs__content">
                    <div class="tabs__content">
                        <x-grid.item :items="$myListItems" />
                    </div>
                </section>
            @else
                <p class="session-message">マイリストを見るにはメール認証が必要です。</p>
            @endif
        @else
            <p class="session-message">マイリストを見るにはログインが必要です。</p>
        @endauth
    @endif
@endsection
