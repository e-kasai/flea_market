<header class="header">
    <a class="header__logo" href="{{ url("/") }}">
        <img class="logo-image" src="{{ asset("img/logo.svg") }}" alt="No Image" />
    </a>
    @unless (request()->routeIs("login", "register.show", "register.store"))
        {{-- 検索フォーム --}}
        <form class="header__search" action="{{ route("items.index") }}" method="GET">
            <input
                class="search__input"
                type="text"
                name="keyword"
                value="{{ request("keyword") }}"
                placeholder="何をお探しですか？"
            />
        </form>
        <nav class="header__nav">
            {{-- ログイン時はログアウト、ゲストにはログインを表示 --}}
            @auth
                <form class="logout-form" action="{{ route("logout") }}" method="POST">
                    @csrf
                    <button class="logout-form__btn" type="submit">ログアウト</button>
                </form>
            @else
                <a class="nav__link" href="{{ route("login") }}">ログイン</a>
            @endauth
            <a class="nav__link nav__link--mypage" href="{{ route("profile.show") }}">マイページ</a>
            {{-- 後でコメントアウト外す、ルート設定後 --}}
            <a class="nav__link nav__link--sell" href="{{ "/sell" }}">出品</a>
        </nav>
    @endunless
</header>
