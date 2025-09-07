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
            <div class="like-area">
                @if ($isFavorited)
                    {{-- いいね済み --}}
                    <form method="POST" action="{{ route("favorite.destroy", $item) }}">
                        @csrf
                        @method("DELETE")
                        <button type="submit" class="like-btn liked" aria-pressed="true">
                            <i class="fa-solid fa-star"></i>
                            {{-- fa-solid 塗りつぶしあり aria-pressed = true = すでに押された状態 --}}
                        </button>
                    </form>
                @else
                    {{-- いいねまだ --}}
                    <form method="POST" action="{{ route("favorite.store", $item) }}">
                        @csrf

                        <button type="submit" class="like-btn unliked" aria-pressed="false">
                            <i class="fa-regular fa-star"></i>
                            {{-- fa-regular 枠だけ塗りつぶしなし aria-pressed = false = まだ押されてない状態 --}}
                        </button>
                    </form>
                @endif
                {{-- いいね数 --}}
                <span class="like-count">{{ $favoritesCount }}</span>
            </div>
            <div>
                <button type="submit" class="comment-btn" aria-pressed="false">
                    <i class="fa-regular fa-comment"></i>
                </button>
                {{-- コメント数 --}}

                @if (isset($comments) && $comments->count())
                    <span class="comments-count">{{ $comments->count() }}</span>
                @else
                    <p>{{ "0" }}</p>
                @endif
            </div>

            <a class="link--purchase" href="{{ route("purchase.show", $item) }}">購入手続きへ</a>

            <section class="product__section">
                <h2 class="product__heading">商品の説明</h2>
                <p>{{ $item->description }}</p>
            </section>

            <section class="product__section">
                <h2 class="product__heading">商品の情報</h2>
                {{-- カテゴリ --}}
                <ul class="kv">
                    <li>
                        <span>カテゴリ</span>
                        <span>{{ $item->categories->pluck("category_name")->join(" ") }}</span>
                    </li>
                    <li>
                        <span>状態</span>
                        <span>{{ $item->condition_label }}</span>
                    </li>
                </ul>
                {{-- 商品の状態 --}}
                {{-- コメント一覧 --}}
                <div class="comment-list">
                    {{-- コメント数 --}}
                    @if (isset($comments) && $comments->count())
                        <h2 class="product__heading">コメント（{{ $comments->count() }})</h2>
                        @foreach ($comments as $comment)
                            {{-- 投稿者情報 --}}
                            <div class="comment__header">
                                <div class="avatar-path__group">
                                    @php
                                        $avatar = optional($comment->user->profile)->avatar_path;
                                    @endphp

                                    @if ($avatar)
                                        <img class="avatar" src="{{ asset("storage/" . $avatar) }}" alt="プロフィール画像" />
                                    @endif
                                </div>
                                <h2 class="profile__name">{{ $comment->user->name }}</h2>
                            </div>
                            {{-- 本文 --}}
                            <p class="comment__body">{{ $comment->body }}</p>
                        @endforeach
                    @else
                        <p class="comment__empty">こちらにコメントが入ります。</p>
                    @endif
                </div>
                {{-- コメントフォーム --}}
                <p>商品へのコメント</p>
                <form method="POST" action="{{ route("comments.store", $item) }}">
                    @csrf
                    <textarea name="body" rows="4" cols="30">{{ old("body") }}</textarea>
                    @error("body")
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="btn btn--primary">コメントを送信する</button>
                </form>
            </section>
        </div>
    </section>
@endsection
