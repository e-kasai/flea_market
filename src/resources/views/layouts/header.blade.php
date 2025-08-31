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
        <header class="header">
            <a class="header__logo" href="{{ url("/") }}">
                <img class="logo-image" src="{{ asset("img/logo.svg") }}" alt="No Image"  />
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
                    <button class="logout-form__btn" type="submit" >ログアウト</button>
                </form>
                <a class="nav__link-mypage" href="/mypage" >マイページ</a>
                <a class="nav__link-sell" href="/sell" >出品</a>
            </nav>
        </header>
    </body>
</html>
