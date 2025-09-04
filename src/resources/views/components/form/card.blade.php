{{-- フォーム全体の共通レイアウト --}}
{{-- resources/views/components/form/card.blade.php --}}

{{-- 親ビューで <x-form.card> ... </x-form.card> の間に書かれた中身がここに差し込まれる --}}

@props(["title" => "", "action" => "#", "method" => "POST", "enctype" => ""])

<section class="form-card">
    {{-- ページごとに異なるフォームタイトルを簡単に変えられる --}}
    <h2 class="form-card__title">{{ $title }}</h2>

    <form class="form-card__contents" method="POST" action="{{ $action }}" enctype="{{ $enctype }}" novalidate>
        @csrf
        {{-- strtoupper は PHPの組み込み関数、文字列をすべて大文字に変換 --}}
        {{-- $methodは親bladeから送られてきたmethod --}}
        @if (strtoupper($method) !== "POST")
            {{-- @methodでPOST以外の場合にPUT や PATCH、DELETE などのHTTPメソッドを擬似的に送信 --}}
            @method($method)
        @endif

        <div class="form-card__inputs">
            {{ $slot }}
            {{-- ここに各画面の入力欄が入る --}}
            {{-- ex <x-form.input name="name" label="ユーザー名" /> --}}
            {{-- ex <x-form.input name="email" label="メールアドレス" type="email" /> --}}
        </div>

        <div class="form-card__actions">
            {{-- 送信ボタンなど、フォーム下部のアクション部分に入れるオプション領域 --}}
            {{-- 以下例： --}}
            {{-- <x-slot name="actions"> --}}
            {{-- <button type="submit" class="btn">送信</button> ←ここがactionsとして読み込まれる --}}
            {{-- </x-slot> --}}
            {{-- 注意点：actionsを使わないと反映されない --}}
            {{ $actions ?? "" }}
        </div>
    </form>
</section>
