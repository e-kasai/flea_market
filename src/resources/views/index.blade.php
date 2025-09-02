<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Contact Form</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/common.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/index.css") }}" />
    </head>

    <body>
        {{-- ヘッダー --}}
        @include("layouts.header")
        <main>
            <section class="tabs">
                {{-- タブでおすすめ、マイリストを切り替え --}}
                {{-- タブ切り替えリンク --}}
                <nav class="tabs__nav">
                    <a
                        href="{{ url("/") }}?tab=recommend&keyword={{ request("keyword") }}"
                        class="{{ $activeTab === "recommend" ? "is-active" : "" }}"
                    >
                        おすすめ
                    </a>
                    <a
                        href="{{ url("/") }}?tab=mylist&keyword={{ request("keyword") }}"
                        class="{{ $activeTab === "mylist" ? "is-active" : "" }}"
                    >
                        マイリスト
                    </a>
                </nav>
                <div class="tabs__switch">
                    {{-- おすすめ一覧 --}}
                    @if ($activeTab === "recommend")
                        <label class="tabs__label" for="tab-recommend"></label>
                        <div class="tabs__content">
                            <section class="item-grid">
                                @foreach ($items as $item)
                                    <div class="item-card">
                                        <img
                                            src="{{ $item->image_path }}"
                                            alt="{{ $item->item_name }}"
                                            class="item-card__img"
                                        />
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
                                @foreach ($myListItems as $item)
                                    <div class="item-card">
                                        <img
                                            src="{{ $item->image_path }}"
                                            alt="{{ $item->item_name }}"
                                            class="item-card__img"
                                        />
                                        <p class="item-card__name">{{ $item->item_name }}</p>
                                    </div>
                                @endforeach
                            </section>
                        </div>
                    @endif
                </div>
            </section>
        </main>
    </body>
</html>
