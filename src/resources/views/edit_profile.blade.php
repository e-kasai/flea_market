@extends("layouts.app")

@push("styles")
    <link rel="stylesheet" href="{{ asset("css/form.css") }}" />
    <link rel="stylesheet" href="{{ asset("css/profile.css") }}" />
@endpush

@section("content")
    <x-form.card title="プロフィール設定" action="{{ route('profile.update') }}" method="PATCH" enctype="multipart/form-data">
        {{-- プロフィール画像 --}}
        <div class="avatar-path__group">
            @if ($profile->avatar_path)
                <img class="avatar" src="{{ asset("storage/" . $profile->avatar_path) }}" alt="プロフィール画像" />
            @endif

            {{-- 画像選択リンク --}}
            <div class="avatar-path">
                {{-- <label for="avatar_path">画像を選択する</label> --}}
                <label class="avatar-path__label" for="avatar_path">画像を選択する</label>
                {{-- <input type="file" id="avatar_path" name="avatar_path" /> --}}
                <input class="avatar-path__input" type="file" id="avatar_path" name="avatar_path" />
                <!-- エラー表示用のインジケーターも追加 -->
                @error("avatar_path")
                    <div class="profile-form__error">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        {{-- ここから下がcard.blade.phpの$slotに入る --}}
        {{-- 名前 --}}
        <x-form.input type="text" name="name" label="ユーザー名" value="{{ old('name',$user->name)}}" required />
        {{-- 郵便番号 --}}
        <x-form.input
            type="text"
            name="postal_code"
            label="郵便番号"
            value="{{ old('postal_code', $profile->postal_code) }}"
            required
        />
        {{-- 住所 --}}
        <x-form.input type="text" name="address" label="住所" value="{{ old('address', $profile->address) }}" required />
        {{-- 建物名 --}}
        <x-form.input type="text" name="building" label="建物名" value="{{ old('building', $profile->building) }}" />
        <x-slot name="actions">
            <button class="btn" type="submit">更新する</button>
        </x-slot>
    </x-form.card>
@endsection
