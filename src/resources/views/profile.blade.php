<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ProfilePage</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/index.css") }}" />
    </head>

    <body>
        {{-- ヘッダー --}}
        <header class="header">
            <div class="header__container">
                <a class="header__logo" href="{{ url("/") }}">
                    <img src="{{ asset("img/logo.svg") }}" alt="No Image" class="logo" />
                </a>
                <form class="header__search" action="/" method="GET">
                    <input
                        class="search__input"
                        type="text"
                        name="keyword"
                        value="{{ request("keyword") }}"
                        placeholder="何をお探しですか？"
                    />
                </form>
                <nav class="header__nav">
                    <form class="logout-form" action="{{ route("logout") }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-form__btn">ログアウト</button>
                    </form>
                    <a href="/mypage" class="nav__link-mypage">マイページ</a>
                    <a href="/sell" class="nav__link-sell">出品</a>
                </nav>
            </div>
        </header>
        {{-- ヘッダーここまで --}}
        <main>
            <section class="profile-info">
                {{-- プロフィール画像(未) --}}
                <img class="profile-info__image" src="img/dog.jpg" alt="NoImage" />
                {{-- ユーザー名 --}}
                <h1 class="user-name">{{ $profile->user->name }}</h1>
                {{-- プロフィールを編集ボタン風リンク --}}
                <a class="user__link-edit" href="{{ route("profile.edit") }}">プロフィールを編集</a>
            </section>
            <section class="item-info">
                {{-- タブで出品、購入一覧を切り替え --}}
                <div class="tab-switch">
                    <label>
                        <input type="radio" name="TAB" checked />
                        出品した商品
                    </label>
                    <div class="tab-content">タブ①の内容をここに表示します</div>
                    <label>
                        <input type="radio" name="TAB" />
                        購入した商品
                    </label>
                    <div class="tab-content">タブ②の内容をここに表示します</div>
                </div>
            </section>
        </main>
    </body>
</html>
