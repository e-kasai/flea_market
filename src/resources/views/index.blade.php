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
        <section class="tabs__content">
            <div class="tabs__content">
                <x-grid.item :items="$myListItems" />
            </div>
        </section>
    @endif
@endsection
