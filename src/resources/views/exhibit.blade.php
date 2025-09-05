@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/exhibit.css") }}" />
@endpush

{{-- 後でエラー一覧は消す予定 --}}
@section("content")
    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    @endif

    {{-- 共通レイアウトに差し込む部分 --}}
    <x-form.card title="" action="{{ route('exhibit.store') }}" method="POST" enctype="multipart/form-data">
        {{-- ----------------------------------- --}}
        <section class="item-create">
            <h1 class="item-create__title">商品の出品</h1>
            <div class="item-create__image">
                <div class="image-path__group">
                    @if ($item->image_path)
                        <img class="item" src="{{ asset("storage/" . $item->image_path) }}" alt="商品画像" />
                    @endif
                </div>
                {{-- 画像選択リンク --}}
                <div class="image-path">
                    <h2 class="image-path__title">商品画像</h2>
                    <label class="image-path__label" for="image_path">画像を選択する</label>
                    <input class="image-path__input" type="file" id="image_path" name="image_path" />
                    @error("image_path")
                        <div class="form-group__error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <h2 class="item-create__subtitle">商品の詳細</h2>

            {{-- カテゴリチェックボックス --}}
            <div class="item-create__categories">
                @foreach ($categories as $category)
                    <label>
                        <input
                            type="checkbox"
                            name="category_ids[]"
                            value="{{ $category->id }}"
                            {{ in_array($category->id, old("category_ids", [])) ? "checked" : "" }}
                        />
                        {{ $category->category_name }}
                    </label>
                @endforeach

                @error("category_ids")
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
            {{-- 商品の状態 select --}}
            <div class="item-create__condition">
                <select name="condition" required>
                    <option value="3">良好</option>
                    <option value="2">目立った傷や汚れなし</option>
                    <option value="1">やや傷や汚れあり</option>
                    <option value="0">状態が悪い</option>
                </select>
                @error("condition")
                    <div class="form-group__error">{{ $message }}</div>
                @enderror
            </div>
        </section>
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
        <x-form.input type="text" name="price" label="販売価格" step="1" required />
        {{-- ボタン --}}
        <x-slot name="actions">
            <button class="btn" type="submit">出品する</button>
        </x-slot>
        {{-- ここまでが$slotに入る --}}
    </x-form.card>
@endsection
