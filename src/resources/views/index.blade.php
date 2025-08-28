<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Contact Form</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/index.css") }}" />
    </head>

    <body>
        <header></header>
        <main>
            <section class="wrapper">
                <div class="container">
                    <div class="content">
                        <ul class="header-nav">
                            @if (Auth::check())
                                <li class="header-nav__item">
                                    <form action="/logout" method="post">
                                        @csrf
                                        <button class="header-nav__button">ログアウト</button>
                                    </form>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
