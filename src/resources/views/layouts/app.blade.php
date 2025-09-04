<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/common.css") }}" />
        {{-- ページ専用CSS --}}
        @stack("styles")
    </head>
    <body>
        @include("layouts.header")
        <main>
            @yield("content")
        </main>
    </body>
</html>
