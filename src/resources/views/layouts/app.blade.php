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
        @stack("styles")
    </head>
    <body>
        @include("layouts.header")
        <main>
            @if (session("message"))
                <div class="session-message">
                    {{ session("message") }}
                </div>
            @endif

            @yield("content")
        </main>
    </body>
</html>
