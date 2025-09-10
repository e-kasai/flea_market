@extends("layouts.app")

@push("styles")
    {{-- 後で設定 --}}
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

    {{-- おすすめ一覧 --}}
    @if ($activeTab === "recommend")
        <section class="tabs__content">
            <div class="tabs__content">
                <x-grid.item :items="$items" />
            </div>
        </section>
    @endif

    {{-- マイリスト一覧 --}}
    @if ($activeTab === "mylist")
        <section class="tabs__content">
            <div class="tabs__content">
                <x-grid.item :items="$myListItems" />
            </div>
        </section>
    @endif
@endsection
