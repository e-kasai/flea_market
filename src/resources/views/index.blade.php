@extends("layouts.app")

@push("styles")
    {{-- 後で設定 --}}
    <link rel="stylesheet" href="{{ asset("css/xxx.css") }}" />
@endpush

@section("content")
    <section class="tabs">
        {{-- タブでおすすめ、マイリストを切り替え --}}
        {{-- タブ切り替えリンク --}}
        <nav class="tabs__nav">
            <a
                href="{{ request()->fullUrlWithQuery(["tab" => "recommend"]) }}"
                class="{{ $activeTab === "recommend" ? "is-active" : "" }}"
            >
                おすすめ
            </a>
            <a
                href="{{ request()->fullUrlWithQuery(["tab" => "mylist"]) }}"
                class="{{ $activeTab === "mylist" ? "is-active" : "" }}"
            >
                > マイリスト
            </a>
        </nav>
        <div class="tabs__switch">
            {{-- おすすめ一覧 --}}
            @if ($activeTab === "recommend")
                <label class="tabs__label" for="tab-recommend"></label>
                <div class="tabs__content">
                    <section class="item-grid">
                        @foreach ($items as $item)
                            @php
                                if (Str::startsWith($item->image_path, "http")) {
                                    // S3など外部URLの場合
                                    $imageRecommend = $item->image_path;
                                } else {
                                    // ローカルストレージの場合
                                    $imageRecommend = asset("storage/" . $item->image_path);
                                }
                            @endphp

                            <div class="item-card">
                                <img src="{{ $imageRecommend }}" alt="{{ $item->item_name }}" class="item-card__img" />
                                <p class="item-card__name">{{ $item->item_name }}</p>
                            </div>
                        @endforeach
                    </section>
                </div>
            @endif

            {{-- マイリスト一覧 --}}
            @if ($activeTab === "mylist")
                <label class="tabs__label" for="tab-mylist"></label>
                <div class="tabs__content">
                    <section class="item-grid">
                        @foreach ($myListItems as $myListItem)
                            @php
                                if (Str::startsWith($myListItem->image_path, "http")) {
                                    // S3など外部URLの場合
                                    $imageMylist = $myListItem->image_path;
                                } else {
                                    // ローカルストレージの場合
                                    $imageMylist = asset("storage/" . $myListItem->image_path);
                                }
                            @endphp

                            <div class="item-card">
                                <img src="{{ $imageMylist }}" alt="{{ $myListItem->item_name }}" class="item-card__img" />
                                <p class="item-card__name">{{ $myListItem->item_name }}</p>
                            </div>
                        @endforeach
                    </section>
                </div>
            @endif
        </div>
    </section>
@endsection
