@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/exhibit.css") }}" />
@endpush

@section("content")
    {{-- 共通レイアウトに差し込む部分 --}}
    <x-form.card title="" action="{{ route('exhibit.store') }}" method="POST" enctype="multipart/form-data">
        {{-- ----------------------------------- --}}
        <h1 class="form-card__title">商品の出品</h1>

        <section class="avatar">
            @if ($item->image_path)
                <img class="item" src="{{ asset("storage/" . $item->image_path) }}" alt="商品画像" />
            @endif

            {{-- 画像選択リンク --}}
            <span>商品画像</span>
            <div class="avatar-path">
                <label class="avatar-path__label" for="image_path">画像を選択する</label>
                <input class="avatar-path__input" type="file" id="image_path" name="image_path" />
            </div>
            @error("image_path")
                <div class="form-error">
                    {{ $message }}
                </div>
            @enderror
        </section>

        <div class="subtitle">
            <h2 class="form-card__subtitle">商品の詳細</h2>
        </div>

        <div class="categories">
            <span>カテゴリー</span>
            <div class="categories__list">
                @foreach ($categories as $category)
                    <label class="category-tag">
                        <input
                            type="checkbox"
                            name="category_ids[]"
                            value="{{ $category->id }}"
                            {{ in_array($category->id, old("category_ids", [])) ? "checked" : "" }}
                        />
                        <span class="chip tag--category-exhibit">{{ $category->category_name }}</span>
                    </label>
                @endforeach
            </div>

            @error("category_ids")
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- 商品の状態 select --}}
        <div class="condition">
            <label for="condition"><span>商品の状態</span></label>

            <select class="condition__select" id="condition" name="condition" required>
                <option value="" disabled selected hidden>選択してください</option>
                <option value="3">良好</option>
                <option value="2">目立った傷や汚れなし</option>
                <option value="1">やや傷や汚れあり</option>
                <option value="0">状態が悪い</option>
            </select>

            @error("condition")
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="subtitle">
            <h2 class="form-card__subtitle">商品名と説明</h2>
        </div>
        {{-- -------------------------------------------- --}}
        {{-- 名前 --}}
        <x-form.input type="text" name="item_name" label="商品名" value="{{ old('item_name') }}" required />
        {{-- ブランド名 --}}
        <x-form.input type="text" name="brand_name" label="ブランド名" value="{{ old('brand_name') }}" required />
        {{-- 商品説明 --}}
        <x-form.input type="textarea" name="description" label="商品の説明" required>
            {{ old("description") }}
        </x-form.input>
        {{-- 販売価格 --}}
        <div class="price-input--with-symbol">
            <x-form.input type="text" name="price" label="販売価格" step="1" required />
        </div>
        {{-- ボタン --}}
        <x-slot name="actions">
            <button class="btn" type="submit">出品する</button>
        </x-slot>
    </x-form.card>
@endsection
