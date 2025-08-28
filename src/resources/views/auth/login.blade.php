<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>COACHTECH</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/login.css") }}" />
    </head>

    <body>
        <header class="header">
            <div class="header__container">
                <a href="{{ url("/") }}" class="header__logo">
                    <img src="{{ asset("img/logo.svg") }}" alt="No Image" class="logo" />
                </a>
            </div>
        </header>
        <main>
            <section class="login">
                <div class="login-form__header">
                    <h2 class="login-form__header-title">ログイン</h2>
                </div>
                <div class="login-form__content">
                    <form class="login-form" action="{{ route("login") }}" method="POST" novalidate>
                        @csrf
                        <div class="login-form__content-group">
                            <label class="login-form__label" for="email">メールアドレス</label>
                            <input
                                class="login-form__input"
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old("email") }}"
                                required
                            />
                            @error("email")
                                <div class="login-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="login-form__content-group">
                            <label class="login-form__label" for="password">パスワード</label>
                            <input class="login-form__input" type="password" id="password" name="password" required />
                            @error("password")
                                <div class="login-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="login-form__button">
                            <button type="submit" class="login-form__button--submit">ログインする</button>
                        </div>
                    </form>
                </div>
                <p class="register-link"><a href="{{ route("register.show") }}">会員登録はこちら</a></p>
            </section>
        </main>
    </body>
</html>
