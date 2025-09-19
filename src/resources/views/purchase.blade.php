@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/purchase.css") }}" />
@endpush

@section("content")
    @php
        if (Str::startsWith($item->image_path, "http")) {
            // S3など外部URLの場合
            $img = $item->image_path;
        } else {
            // ローカルストレージの場合
            $img = asset("storage/" . $item->image_path);
        }
    @endphp

    <section class="purchase">
        <form class="purchase__form" method="POST" action="{{ route("purchase.item", $item) }}" novalidate>
            @csrf

            <div class="purchase__grid">
                {{-- 左側 --}}
                <div class="purchase__left">
                    <div class="purchase__item-info">
                        {{-- 商品画像 --}}
                        <img class="product__img" src="{{ $img }}" alt="{{ $item->item_name }}" />
                        {{-- 商品名 --}}
                        <div class="purchase__item-text">
                            <h1 class="purchase__item-name">{{ $item->item_name }}</h1>
                            {{-- 価格 --}}
                            <p class="purchase__price-detail">
                                <span class="yen">¥</span>
                                {{ number_format($item->price) }}
                            </p>
                        </div>
                    </div>
                    {{-- 支払い方法セレクトボックス --}}
                    <div class="purchase__item-info">
                        <div class="purchase__payment">
                            <label for="payment_method"><span class="purchase__payment-title">支払い方法</span></label>
                            <br />

                            <select class="purchase__payment-select" name="payment_method" id="payment_method" required>
                                <option value="" disabled hidden {{ old("payment_method") ? "" : "selected" }}>
                                    選択してください
                                </option>
                                <option value="1" {{ old("payment_method") === "1" ? "selected" : "" }}>コンビニ払い</option>
                                <option value="2" {{ old("payment_method") === "2" ? "selected" : "" }}>カード支払い</option>
                            </select>
                            @error("payment_method")
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="purchase__address">
                        <div class="purchase__address-header">
                            <h2 class="purchase__address-title">配送先</h2>
                            <a class="link" href="{{ route("address.show", $item) }}">変更する</a>
                        </div>
                        <p class="purchase__address-line">
                            <span class="postal">〒</span>
                            {{ $shipping["postal_code"] ?? "" }}
                        </p>
                        <p class="purchase__address-line">{{ $shipping["address"] ?? "" }}</p>
                        <p class="purchase__address-line">{{ $shipping["building"] ?? "" }}</p>

                        @if (! $canPurchase)
                            <p class="alert">
                                住所が未登録です。
                                <a href="{{ route("profile.edit") }}">プロフィール編集</a>
                                から登録してください。
                            </p>
                        @endif
                    </div>
                </div>

                {{-- 右側 --}}
                <div class="purchase__right">
                    <div class="purchase__summary">
                        <div class="purchase__summary-row">
                            <p class="purchase__summary-title">商品代金</p>
                            <p class="purchase__summary-price">
                                <span class="yen">¥</span>
                                {{ number_format($item->price) }}
                            </p>
                        </div>
                        @php
                            $paymentLabels = [
                                0 => "選択してください",
                                1 => "コンビニ払い",
                                2 => "カード支払い",
                            ];
                        @endphp

                        <div class="purchase__summary-row">
                            <p class="purchase__payment-header">支払い方法</p>
                            <p class="purchase__payment-preview" id="payment_preview">
                                {{ old("payment_method") ? $paymentLabels[(int) old("payment_method")] ?? "未選択" : "未選択" }}
                            </p>
                        </div>
                    </div>
                    <button class="purchase__button" type="submit" {{ $canPurchase ? "" : "disabled" }}>購入する</button>
                </div>
            </div>
        </form>
    </section>
@endsection

@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('payment_method');
            const preview = document.querySelector('[id="payment_preview"]');

            if (!select || !preview) return;

            // value→ラベルの対応（Blade側と一致）
            const labels = {
                1: 'コンビニ払い',
                2: 'カード支払い',
            };

            const update = () => {
                const val = select.value;
                preview.textContent = labels[val] ?? '未選択';
            };

            // 初期表示（old値反映）
            update();

            // 変更即時反映
            select.addEventListener('change', update);
        });
    </script>
@endpush
