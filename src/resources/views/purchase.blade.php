@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/purchase.css") }}" />
@endpush

@section("content")
    {{-- あとでアクセサにいれるか検討 --}}
    @php
        if (Str::startsWith($item->image_path, "http")) {
            // S3など外部URLの場合
            $img = $item->image_path;
        } else {
            // ローカルストレージの場合
            $img = asset("storage/" . $item->image_path);
        }
    @endphp

    <section class="item">
        <form method="POST" action="{{ route("purchase.item", $item) }}" novalidate>
            @csrf
            {{-- ここから画面左側の領域 --}}
            <div class="item-info">
                {{-- 商品画像 --}}
                <div class="product__media">
                    <img src="{{ $img }}" alt="{{ $item->item_name }}" class="product__img" />
                </div>
                {{-- 商品名 --}}
                <h1>{{ $item->item_name }}</h1>
            </div>
            {{-- 価格 --}}
            <p>
                ¥{{ number_format($item->price) }}
                <small>（税込）</small>
            </p>
            {{-- 支払い方法セレクトボックス --}}
            <div>
                <label for="payment-method"><span>支払い方法</span></label>
                <br />

                <select id="payment-method" name="payment_method" required>
                    <option value="">選択してください</option>
                    <option value="1" @selected(old("payment_method") === "1")>コンビニ払い</option>
                    <option value="2" @selected(old("payment_method") === "2")>カード支払い</option>
                </select>
                @error("payment_method")
                    <div>{{ $message }}</div>
                @enderror
            </div>

            <p>配送先</p>
            <a href="{{ route("address.show",$item) }}">変更する</a>
            {{-- 配送先の表示（プロフィールで登録した住所） --}}
            <p>{{ $shipping['postal_code'] ?? '' }}</p>
            <p>{{ $shipping['address'] ?? ''}}</p>
            <p>{{ $shipping['building'] ?? ''}}</p>

            @if (! $canPurchase)
                <p class="alert">
                    住所が未登録です。
                    <a href="{{ route("profile.edit") }}">プロフィール編集</a>
                    から登録してください。
                </p>
            @endif

            {{-- ここから画面右側の領域 --}}
            {{-- 商品代金 = 価格 --}}
            <p>
                ¥{{ number_format($item->price) }}
                <small>（税込）</small>
            </p>
            {{-- 支払い方法 = 選んだやつ --}}
            {{-- 購入ボタン --}}

            <button type="submit" {{ $canPurchase ? "" : "disabled" }}>購入する</button>
        </form>
    </section>
@endsection
