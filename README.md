# ポートフォリオ: CINEMA-HOUSE

小規模なプライベートシネマの座席予約をイメージした **Laravel × Vue3 (Inertia.js)** のアプリです。  
管理者が映画・上映スケジュールを登録し、ユーザーが上映スケジュールから座席を予約できます。  
このプロジェクトは、フロントエンド、バックエンド、データベース管理、デプロイメントなど、ウェブ開発における個人用ポートフォリオになりますのでご了承ください。

## 開発環境
- バックエンド言語：PHP8.4
- データベース：MySQL9.3
- Webサーバー：Nginx
- JavaScript実行環境：Node.js 22.x

## 使用技術
| 分類      | 使用技術               |
| ------- | ------------------ |
| バックエンド  | Laravel 12         |
| フロントエンド | Vue 3 + TypeScript |
| ページ遷移   | Inertia.js         |
| UI      | Element Plus       |
| CSS     | Tailwind CSS       |
| 開発環境    | Docker（ローカル開発想定）   |
| デプロイ環境  | さくらVPS     |

## 主な機能

### 管理者側
- 映画情報の追加および管理
- 上映スケジュールを作成および管理
- タスクスケジュールによる上映スケジュールの削除（過去1週間前日付）
- 管理者側でのユーザー管理機能
- 全ユーザーに対する一斉通知メール送信（Queueによる非同期処理）

### ユーザー側
- 上映スケジュールから座席予約
- 予約履歴一覧を表示し、予約情報をPDFとして出力
- 映画に対する「いいね」機能

### 認証構成
- マルチログイン対応（**admin** / **web**）
- セッション分離（クッキー名変更による衝突回避）

## 環境セットアップ

### 条件
- システムに Docker および Docker Compose がインストールされていること
- Git がインストールされていること
- ビルド作業の自動化ツール「Makefile」は必須ではありませんがあると便利です

### 手順
1. リポジトリをクローン:
```bash
git clone git@github.com:taisuke0804/cinema-house-portfolio.git
```

2. 必要に応じて環境変数を設定:
   `/.env` ファイルを開き、設定を更新してください

3. Docker コンテナをビルドして起動:
```bash
docker compose up -d --build
```

4. アプリケーション実行のため各種コマンド実行とマイグレーション:
```bash
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate:fresh --seed --seeder=DevelopmentSeeder
docker compose exec app chmod -R 777 storage bootstrap/cache
docker compose exec app npm run dev
```

5. アプリケーションにアクセス:
- ユーザーインターフェース: [http://localhost](http://localhost)
- 管理者パネル: [http://localhost/admin/login](http://localhost/admin/login)

## Git運用ルール

### ブランチ命名規則
このプロジェクトでは、以下のGitブランチ戦略を採用しています:
- `feature/<チケット番号>-<機能名>` 新機能の場合。
- `bugfix/<チケット番号>-<バグ内容>` バグ修正の場合。
- `improvement/<チケット番号>-<改良内容>` 機能改良の場合。
- `hotfix/<チケット番号>-<緊急修正内容>` 緊急修正の場合。
#### 例:
```bash
git checkout -b feature/0001-add-movie-schedule
```

### コミットメッセージ規則
| type     | 用途           |
| -------- | ------------ |
| feat     | 新機能追加        |
| fix      | バグ修正         |
| docs     | ドキュメント修正     |
| refactor | リファクタリング     |
| chore    | ライブラリ更新・環境設定 |

## 今後の追加予定機能
- CSVファイルによるインポート機能
- 管理者側の2段階認証機能

## ライセンス
このプロジェクトは [MITライセンス](LICENSE) の下でライセンスされています。
---

プロジェクトをご覧いただきありがとうございます！ぜひフィードバックをよろしくお願いします。