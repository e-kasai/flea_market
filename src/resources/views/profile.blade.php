<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ProfilePage</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset("css/sanitize.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/common.css") }}" />
        <link rel="stylesheet" href="{{ asset("css/profile.css") }}" />
    </head>

    <body>
        {{-- ヘッダー --}}
        @include("layouts.header")
        {{-- ヘッダーここまで --}}
        <main>
            <section class="profile-info">
                {{-- プロフィール画像 --}}
                <img
                    class="profile-avatar"
                    src="{{
                        $userProfile->avatar_path
                            ? asset("storage/" . $userProfile->avatar_path)
                            : asset("img/dog.jpg")
                    }}"
                    alt="プロフィール画像"
                />
                {{-- ユーザー名 --}}
                <h1 class="user-name">{{ $currentUser->user->name }}</h1>
                {{-- プロフィールを編集ボタン風リンク --}}
                <a class="user__link-edit" href="{{ route("profile.edit") }}">プロフィールを編集</a>
            </section>
            <section class="item-info">
                {{-- タブで出品、購入一覧を切り替え --}}
                <div class="tab-switch">
                    <label>
                        <input type="radio" name="TAB" checked />
                        出品した商品
                    </label>
                    <div class="tab-content">まだ出品データがありません</div>
                    <label>
                        <input type="radio" name="TAB" />
                        購入した商品
                    </label>
                    <div class="tab-content">まだ購入データがありません</div>
                </div>
            </section>
        </main>
    </body>
</html>
