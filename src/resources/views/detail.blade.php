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
        <img class="product__img" src="{{ $img }}" alt="{{ $item->item_name }}" />

        {{-- 右：テキスト系コンテンツ --}}
        <div class="product__body">
            <h1 class="product__title">{{ $item->item_name }}</h1>
            <p class="product__brand">{{ $item->brand_name }}</p>
            <p class="product__price">
                ¥{{ number_format($item->price) }}
                <small>（税込）</small>
            </p>
            <div class="icons">
                <div class="like">
                    @if ($isFavorited)
                        {{-- いいね済み --}}
                        <form method="POST" action="{{ route("favorite.destroy", $item) }}">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="icon-btn" aria-pressed="true">
                                <i class="fa-solid fa-star"></i>
                                {{-- fa-solid 塗りつぶしあり aria-pressed = true = すでに押された状態 --}}
                            </button>
                        </form>
                    @else
                        {{-- いいねまだ --}}
                        <form method="POST" action="{{ route("favorite.store", $item) }}">
                            @csrf

                            <button type="submit" class="icon-btn" aria-pressed="false">
                                <i class="fa-regular fa-star"></i>
                                {{-- fa-regular 枠だけ塗りつぶしなし aria-pressed = false = まだ押されてない状態 --}}
                            </button>
                        </form>
                    @endif
                    {{-- いいね数 --}}
                    <span class="count">{{ $displayFavoritesCount }}</span>
                </div>
                <div class="comment">
                    <button type="submit" class="icon-btn" aria-pressed="false">
                        <i class="fa-regular fa-comment"></i>
                    </button>
                    {{-- コメント数 --}}
                    <span class="count">{{ $comments->count() ?? 0 }}</span>
                </div>
            </div>

            <a class="link--purchase" href="{{ route("purchase.show", $item) }}">購入手続きへ</a>

            <section class="product__section">
                <h2 class="product__heading">商品の説明</h2>
                <p>{{ $item->description }}</p>
            </section>

            <section class="product__section">
                <h2 class="product__heading">商品の情報</h2>
                {{-- カテゴリ --}}
                <ul class="product__details">
                    <li>
                        <span class="key">カテゴリ</span>
                        <span class="value category">
                            @foreach ($item->categories as $categoryName)
                                <span class="chip">{{ $categoryName->category_name }}</span>
                            @endforeach
                        </span>
                    </li>
                    <li>
                        <span class="key">商品の状態</span>
                        <span class="value condition">{{ $item->condition_label }}</span>
                    </li>
                </ul>
                {{-- 商品の状態 --}}
                {{-- コメント一覧 --}}
                <div class="comment-list">
                    {{-- コメント数 --}}
                    @if (isset($comments) && $comments->count())
                        <h2 class="product__heading">コメント({{ $comments->count() }})</h2>
                        @foreach ($comments as $comment)
                            {{-- 投稿者情報 --}}
                            <div class="comment__users">
                                @php
                                    $avatar = optional($comment->user->profile)->avatar_path;
                                    $avatarPath = $avatar ? asset("storage/" . $avatar) : asset("img/noimage.png");
                                @endphp

                                <img class="comment__users-avatar" src="{{ $avatarPath }}" alt="プロフィール画像" />
                                <p class="comment__user-name">{{ $comment->user->name }}</p>
                            </div>
                            {{-- 本文 --}}
                            <p class="comment__body">{{ $comment->body }}</p>
                        @endforeach
                    @else
                        <p class="product__heading">コメント(0)</p>
                    @endif
                </div>
                {{-- コメントフォーム --}}
                <p class="comment-form__title">商品へのコメント</p>
                <form method="POST" action="{{ route("comments.store", $item) }}">
                    @csrf
                    <textarea name="body" rows="4" cols="30">{{ old("body") }}</textarea>
                    @error("body")
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="btn">コメントを送信する</button>
                </form>
            </section>
        </div>
    </section>
@endsection
