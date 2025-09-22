<header class="header">
    <a class="header__logo" href="{{ route("items.index") }}">
        <img class="logo-image" src="{{ asset("img/logo.svg") }}" alt="No Image" />
    </a>
    @unless (request()->routeIs("login", "register.*", "verification.*"))
        <form class="header__search" action="{{ route("items.index") }}" method="GET" role="search">
            <input
                class="header__search-input"
                type="text"
                name="keyword"
                value="{{ request("keyword") }}"
                placeholder="なにをお探しですか？"
            />
        </form>
        <nav class="nav">
            {{-- ログイン時はログアウト表示、ゲスト時はログインを表示 --}}
            @auth
                <form class="nav__logout-form" action="{{ route("logout") }}" method="POST">
                    @csrf
                    <button class="nav__logout-btn" type="submit">ログアウト</button>
                </form>
            @else
                <a class="nav__link" href="{{ route("login") }}">ログイン</a>
            @endauth
            <a class="nav__link nav__link--mypage" href="{{ route("profile.show") }}">マイページ</a>
            <a class="nav__link nav__link--sell" href="{{ route("exhibit.show") }}">出品</a>
        </nav>
    @endunless
</header>
