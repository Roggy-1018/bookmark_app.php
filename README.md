PHP製 ブックマーク管理アプリ
PHPとMySQLで作成した、シンプルなブックマーク管理アプリです。ブックマークの登録と一覧表示ができます。

✨ 主な機能
ブックマークの登録（タイトル・URL）

登録済みブックマークの一覧表示

💻 動作環境 (ローカル)
XAMPP (Apache, MySQL)

🛠️ ローカル環境でのセットアップ方法
ファイルの配置
XAMPPのhtdocsディレクトリ内にbookmark_appという名前のフォルダを作成し、その中にindex.phpを配置します。

xampp/
└── htdocs/
    └── bookmark_app/
        └── index.php
XAMPPの起動
XAMPPコントロールパネルを起動し、ApacheとMySQLの両方を開始します。

データベースの作成
ブラウザでhttp://localhost/phpmyadmin/にアクセスし、bookmark_appという名前で新しいデータベースを作成します。

テーブルの作成
作成したbookmark_appデータベースを選択し、以下のSQLクエリを実行してbookmarksテーブルを作成します。

SQL

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
動作確認
ブラウザで http://localhost/bookmark_app/ にアクセスし、アプリが表示されればセットアップは完了です。

🚀 本番環境へのデプロイ (さくらサーバー等)
ファイルのアップロード
index.phpファイルを、サーバーの公開ディレクトリ（例: /home/user/www/）にFTPソフト等でアップロードします。

データベースの作成
サーバーのコントロールパネル（さくらのレンタルサーバなど）で、本番用のデータベースとユーザーを作成します。

接続情報の書き換え
アップロードしたindex.phpを開き、冒頭のデータベース接続情報を、サーバーから提供された情報に書き換えます。

PHP

// さくらサーバー等から指定された情報に書き換える
define('DB_HOST', 'mysqlXXX.db.sakura.ne.jp'); // DBホスト名
define('DB_NAME', 'your_database_name');   // DB名
define('DB_USER', 'your_username');         // ユーザー名
define('DB_PASS', 'your_password');         // パスワード
