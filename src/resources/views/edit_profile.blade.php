<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Contact Form</title>
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/common.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/edit_profile.css") }}" />
    </head>

    <body>
        <header>@include("layouts.header")</header>
        <main>
            <section class="update-profile">
                <div class="update-profile__header">
                    <h2 class="update-profile__header-title">プロフィール設定</h2>
                </div>
                {{-- プロフィール画像 --}}
                @if ($userProfile->avatar)
                    <img
                        class="profile-avatar"
                        src="{{ asset("storage/material_images/" . $userProfile->avatar_path) }}"
                        alt="プロフィール画像"
                    />
                @endif

                <div class="update-profile__content">
                    <form
                        class="profile-form"
                        action="{{ route("profile.update") }}"
                        method="POST"
                        enctype="multipart/form-data"
                        novalidate
                    >
                        @csrf
                        @method("PATCH")

                        {{-- 画像選択リンク --}}
                        <div class="profile-form__content-group">
                            {{-- <label for="avatar_path">画像を選択する</label> --}}
                            <label class="profile-form__label" for="avatar_path">画像を選択する</label>
                            {{-- <input type="file" id="avatar_path" name="avatar_path" /> --}}
                            <input class="profile-form__input" type="file" id="avatar_path" name="avatar_path" />
                            <!-- エラー表示用のインジケーターも追加 -->
                            @error("avatar_path")
                                <div class="profile-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        {{-- ユーザー名 --}}
                        <div class="profile-form__content-group">
                            <label class="profile-form__label" for="name">ユーザー名</label>
                            <input
                                class="profile-form__input"
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old("name", $currentUser->name) }}"
                                required
                            />
                            @error("name")
                                <div class="profile-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        {{-- 郵便番号 --}}
                        <div class="profile-form__content-group">
                            <label class="profile-form__label" for="postal_code">郵便番号</label>
                            <input
                                class="profile-form__input"
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                value="{{ old("postal_code", $userProfile->postal_code) }}"
                                required
                            />
                            @error("postal_code")
                                <div class="profile-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        {{-- 住所 --}}
                        <div class="profile-form__content-group">
                            <label class="profile-form__label" for="address">住所</label>
                            <input
                                class="profile-form__input"
                                type="text"
                                id="address"
                                name="address"
                                value="{{ old("address", $userProfile->address) }}"
                                required
                            />
                            @error("address")
                                <div class="profile-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        {{-- 建物名 --}}
                        <div class="profile-form__content-group">
                            <label class="profile-form__label" for="building">建物名</label>
                            <input
                                class="profile-form__input"
                                type="text"
                                id="building"
                                name="building"
                                value="{{ old("building", $userProfile->building) }}"
                                required
                            />
                            @error("building")
                                <div class="profile-form__error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        {{-- 更新するボタン --}}
                        <div class="profile-form__button">
                            <button type="submit" class="profile-form__button--submit">更新する</button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </body>
</html>
