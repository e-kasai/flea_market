@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/detail.css") }}" />
@endpush

@section("content")
    <section class="product">
        {{-- 左：画像 --}}
        <img class="product__img" src="{{ $item->image_url }}" alt="{{ $item->item_name }}" />

        {{-- 右：テキスト系コンテンツ --}}
        <div class="product__body">
            <h1 class="product__title">{{ $item->item_name }}</h1>
            <p class="product__brand">{{ $item->brand_name }}</p>
            <p class="product__price">
                ¥{{ number_format($item->price) }}
                <span class="tax-included">(税込)</span>
            </p>
            <div class="product__actions">
                <div class="product__like">
                    @if ($isFavorited)
                        {{-- いいね済み --}}
                        <form method="POST" action="{{ route("favorite.destroy", $item) }}">
                            @csrf
                            @method("DELETE")
                            <button type="submit" class="icon-btn" aria-pressed="true">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        </form>
                    @else
                        {{-- いいねまだ --}}
                        <form method="POST" action="{{ route("favorite.store", $item) }}">
                            @csrf
                            <button type="submit" class="icon-btn" aria-pressed="false">
                                <i class="fa-regular fa-star"></i>
                            </button>
                        </form>
                    @endif
                    <span class="count">{{ $displayFavoritesCount }}</span>
                </div>
                <div class="product__comment">
                    <button type="submit" class="icon-btn" aria-pressed="false">
                        <i class="fa-regular fa-comment"></i>
                    </button>
                    <span class="count">{{ $comments->count() ?? 0 }}</span>
                </div>
            </div>

            <a class="link--purchase" href="{{ route("purchase.show", $item) }}">購入手続きへ</a>

            <section class="product__section">
                <h2 class="product__heading">商品説明</h2>
                <p class="product__description">{{ $item->description }}</p>
            </section>

            <section class="product__section">
                <h2 class="product__heading">商品の情報</h2>
                {{-- カテゴリ --}}
                <ul class="product__details">
                    <li>
                        <span class="key">カテゴリー</span>
                        <span class="value category">
                            @foreach ($item->categories as $categoryName)
                                <span class="chip chip--category">{{ $categoryName->category_name }}</span>
                            @endforeach
                        </span>
                    </li>
                    <li>
                        <span class="key">商品の状態</span>
                        <span class="value condition">{{ $item->condition_label }}</span>
                    </li>
                </ul>

                <div class="comment">
                    @if (isset($comments) && $comments->count())
                        <h2 class="comment__heading">コメント({{ $comments->count() }})</h2>
                        @foreach ($comments as $comment)
                            <div class="comment__users">
                                @php
                                    $avatar = optional($comment->user->profile)->avatar_path;
                                    $avatarPath = $avatar ? asset("storage/" . $avatar) : asset("img/noimage.png");
                                @endphp

                                <img class="comment__users-avatar" src="{{ $avatarPath }}" alt="プロフィール画像" />
                                <p class="comment__users-name">{{ $comment->user->name }}</p>
                            </div>
                            <p class="comment__body">{{ $comment->body }}</p>
                        @endforeach
                    @else
                        <p class="comment__heading">コメント(0)</p>
                    @endif
                </div>
                <p class="comment-form__title">商品へのコメント</p>
                <form method="POST" action="{{ route("comments.store", $item) }}">
                    @csrf
                    <textarea class="comment-textarea" name="body" rows="4" cols="30">{{ old("body") }}</textarea>
                    @error("body")
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="btn">コメントを送信する</button>
                </form>
            </section>
        </div>
    </section>
@endsection
