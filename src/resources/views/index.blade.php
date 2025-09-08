@extends("layouts.app")

@push("styles")
    {{-- 後で設定 --}}
    <link rel="stylesheet" href="{{ asset("css/grid.css") }}" />
@endpush

@section("content")
    <section class="tabs">
        {{-- タブでおすすめ、マイリストを切り替え --}}
        {{-- タブ切り替えリンク --}}
        @php
            $tabs = [
                ["label" => "おすすめ", "href" => request()->fullUrlWithQuery(["tab" => "recommend"]), "active" => $activeTab === "recommend" ? "is-active" : ""],
                ["label" => "マイリスト", "href" => request()->fullUrlWithQuery(["tab" => "mylist"]), "active" => $activeTab === "mylist"],
            ];
        @endphp

        <x-tabs.nav :items="$tabs" />
        <div class="tabs__switch">
            {{-- おすすめ一覧 --}}
            @if ($activeTab === "recommend")
                <label class="tabs__label" for="tab-recommend"></label>
                <div class="tabs__content">
                    <x-grid.item :items="$items" />
                </div>
            @endif

            {{-- マイリスト一覧 --}}
            @if ($activeTab === "mylist")
                <label class="tabs__label" for="tab-mylist"></label>
                <div class="tabs__content">
                    <x-grid.item :items="$myListItems" />
                </div>
            @endif
        </div>
    </section>
@endsection
