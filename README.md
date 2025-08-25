---
# アプリケーション名

### coachtechフリマ
---

#### 概要

以下の機能を実装したフリマアプリです

- ユーザー登録・ログイン機能
- マイページ（編集機能/出品した商品一覧/いいねした商品一覧）
- 商品出品
- 商品一覧/詳細表示
- 商品名部分一致による商品検索
- 商品購入（コンビニ/クレカ払い)
- お気に入り（いいね）機能
- 商品詳細画面でのコメント機能

---

## 環境構築

### Docker ビルド

1. git clone

```bash
git clone git@github.com:e-kasai/flea_market.git     // SSHの場合はこちら
git clone https://github.com/e-kasai/flea_market.git // HTTPSの場合はこちら
```

2. Docker 立ち上げ

```bash
cd flea_market
docker-compose up -d --build
```

### Laravel 環境構築

1. `docker-compose exec php bash`
2. `composer install`
3. `docker compose exec php cp .env.example .env`

4. `.env`に環境変数を追加(具体的な値は slack で共有します)

5. アプリケーションキーの作成

```bash
php artisan key:generate
```

6. マイグレーションの実行

```bash
php artisan migrate
```

7. シーディングの実行

```bash
php artisan db:seed
```

### 環境依存について

Mac の M1・M2 チップの PC の場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.yml ファイルの「mysql」セクションに「platform」の項目を追加で記載してください

```bash
mysql:
    platform: linux/x86_64  //この文追加
    image: mysql:8.0.26
    environment:
```

---

## 使用技術

- Laravel 8.83.29
- PHP 7.4.9
- MySQL 8.0.26
- Docker/docker-compose

---

## ER 図

![ER図](./docs/er.png)

---

## URL

- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
