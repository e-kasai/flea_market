<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/common.css") }}" />
        {{-- Font Awesome v6（solid/regular/brands まとめて） --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
        {{-- ページ専用CSS --}}
        <link rel="stylesheet" href="{{ asset("css/email.css") }}" />
    </head>
    <body>
        <header class="header">
            <a class="header__logo" href="{{ url("/") }}">
                <img class="logo-image" src="{{ asset("img/logo.svg") }}" alt="No Image" />
            </a>
        </header>
        <main>
            @if (session("message"))
                <div class="session-message">
                    {{ session("message") }}
                </div>
            @endif

            <p class="verify__message">
                登録していただいたメールアドレスに認証メールを送付しました。
                <br />
                メール認証を完了してください。
            </p>
            <a href="http://localhost:8025">認証はこちらから</a>
            <form method="POST" action="{{ route("verification.send") }}">
                @csrf
                <button type="submit">認証メールを再送する</button>
            </form>
        </main>
    </body>
</html>
