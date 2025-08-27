<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>COACHTECH</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/register.css") }}" />
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
            <section class="register">
                <div class="register-form__header">
                    <h2 class="register-form__header-title">会員登録</h2>
                </div>
                <div class="register-form__content">
                    <form class="register-form" action="{{ route("register.store") }}" method="POST" novalidate>
                        @csrf
                        <div class="register-form__content-group">
                            <label class="register-form__label" for="name">ユーザー名</label>
                            <input class="register-form__input" type="text" id="name" name="name" value="{{ old("name") }}" required />
                            @error("name")
                                <div class="register-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="register-form__content-group">
                            <label class="register-form__label" for="email">メールアドレス</label>
                            <input class="register-form__input" type="email" id="email" name="email" value="{{ old("email") }}" required />
                            @error("email")
                                <div class="register-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="register-form__content-group">
                            <label class="register-form__label" for="password">パスワード</label>
                            <input class="register-form__input" type="password" id="password" name="password" required />
                            @error("password")
                                <div class="register-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="register-form__content-group">
                            <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
                            <input
                                class="register-form__input"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                            />
                            @error("password_confirmation")
                                <div class="register-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="register-form__button">
                            <button type="submit" class="register-form__button--submit">登録する</button>
                        </div>
                    </form>
                </div>
                <p class="login-link"><a href="{{ route("login") }}">ログインはこちら</a></p>
            </section>
        </main>
    </body>
</html>
