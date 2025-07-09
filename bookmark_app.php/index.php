<?php

// ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
// さくらサーバーにデプロイする際は、この中の情報を書き換える
// ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
// データベース接続情報
define('DB_HOST', 'localhost');
define('DB_NAME', 'bookmark_app'); // 1.で作成したデータベース名
define('DB_USER', 'root');      // XAMPPのデフォルトユーザー名
define('DB_PASS', '');          // XAMPPのデフォルトパスワード
// ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲
// ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

// 文字コード設定
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

// データベース接続
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // 接続エラーの際に画面にエラー内容を表示する
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit('データベース接続に失敗しました。' . $e->getMessage());
}

// ---- データ登録処理 ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを取得
    $title = $_POST['title'] ?? '';
    $url = $_POST['url'] ?? '';

    // 簡単なバリデーション
    if ($title !== '' && $url !== '') {
        // SQLインジェクション対策としてプリペアドステートメントを使用
        $stmt = $pdo->prepare('INSERT INTO bookmarks (title, url) VALUES (:title, :url)');
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':url', $url, PDO::PARAM_STR);
        $stmt->execute();

        // 登録後に自身にリダイレクトして二重投稿を防止
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit;
    }
}

// ---- データ表示処理 ----
// 登録されているブックマークをすべて取得
$stmt = $pdo->query('SELECT * FROM bookmarks ORDER BY id DESC');
$bookmarks = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブックマークアプリ</title>
    <style>
        body { font-family: sans-serif; margin: 2em; }
        h1, h2 { color: #333; }
        form { background: #f4f4f4; padding: 20px; border-radius: 5px; margin-bottom: 2em; }
        form div { margin-bottom: 10px; }
        form label { display: inline-block; width: 80px; }
        form input[type="text"], form input[type="url"] { width: 250px; padding: 5px; }
        form button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 3px; }
        .bookmark-list { list-style: none; padding: 0; }
        .bookmark-item { background: #fff; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .bookmark-item a { text-decoration: none; font-weight: bold; color: #0056b3; }
        .bookmark-item p { font-size: 0.8em; color: #777; margin: 5px 0 0; }
    </style>
</head>
<body>

    <h1>ブックマーク登録 🔖</h1>
    <form action="index.php" method="post">
        <div>
            <label for="title">タイトル:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="url">URL:</label>
            <input type="url" id="url" name="url" required>
        </div>
        <div>
            <button type="submit">登録</button>
        </div>
    </form>

    <h2>ブックマーク一覧 ✨</h2>
    <ul class="bookmark-list">
        <?php foreach ($bookmarks as $bookmark): ?>
            <li class="bookmark-item">
                <?php // XSS対策としてhtmlspecialcharsを使用 ?>
                <a href="<?php echo htmlspecialchars($bookmark['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    <?php echo htmlspecialchars($bookmark['title'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <p>登録日: <?php echo htmlspecialchars($bookmark['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>

</body>
</html>