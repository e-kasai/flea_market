@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/detail.css") }}" />
@endpush

@section("content")
    @php
        if (Str::startsWith($item->image_path, "http")) {
            // S3など外部URLの場合
            $img = $item->image_path;
        } else {
            // ローカルストレージの場合
            $img = asset("storage/" . $item->image_path);
        }
    @endphp

    <section class="product">
        {{-- 左：画像 --}}
        <div class="product__media">
            <img src="{{ $img }}" alt="{{ $item->item_name }}" class="product__img" />
        </div>

        {{-- 右：テキスト系コンテンツ --}}
        <div class="product__body">
            <h1 class="product__title">{{ $item->item_name }}</h1>
            <p class="product__brand">{{ $item->brand_name }}</p>
            <p class="product__price">
                ¥{{ number_format($item->price) }}
                <small>（税込）</small>
            </p>

            {{-- いいね --}}
            <div class="like-area">
                <span class="like-count">{{ $favoritesCount }}</span>

                @auth
                    @if ($isFavorited)
                        <form method="POST" action="{{ route("favorite.destroy", ["item_id" => $item->id]) }}">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="like-btn liked" aria-pressed="true">
                                <i class="fa-solid fa-heart"></i>
                                {{-- 塗りつぶし＆色付き --}}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route("favorite.store", ["item_id" => $item->id]) }}">
                            @csrf
                            <button type="submit" class="like-btn" aria-pressed="false">
                                <i class="fa-regular fa-heart"></i>
                                {{-- 反転アイコン --}}
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            <form action="{{ route("purchase.show", $item) }}" method="POST">
                @csrf
                <button class="btn btn--primary">購入手続きへ</button>
            </form>

            <section class="product__section">
                <h2 class="product__heading">商品の説明</h2>
                <p>{{ $item->description }}</p>
            </section>

            <section class="product__section">
                <h2 class="product__heading">商品の情報</h2>
                {{-- 例：カテゴリなど --}}
                <ul class="kv">
                    <li>
                        <span>カテゴリ</span>
                        <span>{{ $item->categories->pluck("category_name")->join(" ") }}</span>
                    </li>
                    <li>
                        <span>状態</span>
                        <span>{{ $item->condition }}</span>
                    </li>
                </ul>
            </section>
        </div>
    </section>
@endsection
