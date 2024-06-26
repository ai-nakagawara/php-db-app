<?php 
$dsn = 'mysql:dbname=ywl3cfeoy8pwxya0;host=iu51mf0q32fkhfpl.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;charset=utf8mb4';
$user = 'pnslnqkdktbrdsc1';
$password = 'cvwir4xay91lhxp9';

try {
    $pdo = new PDO($dsn, $user, $password);

    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    } else {
        $order = NULL;
    }

    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    } else {
        $keyword = NULL;
    }

    if ($order === 'desc') {
        $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at DESC';
    } else {
        $sql_select = 'SELECT * FROM products WHERE product_name LIKE :keyword ORDER BY updated_at ASC';
    }

    $stmt_select = $pdo->prepare($sql_select);

    // SQLのLIKE句で使うため、変数$keywordの前後を％で囲む
    // 補足：partial match=部分一致
    $partial_match = "%{$keyword}%";

    $stmt_select->bindValue(':keyword', $partial_match, PDO::PARAM_STR);

    $stmt_select->execute();

    $products = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fontsの読み込み -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav>
            <a href="index.php">商品管理アプリ</a>
        </nav>
    </header>
    <main>
        <article class="products">
            <h1>商品一覧</h1>
            <?php 
            // messageパラメータの値を受けっていれば、それを表示する
            if (isset($_GET['message'])) {
                echo "<p class='success'>{$_GET['message']}</p>";
            }
            ?>
            <div class="products-ui">
                <div>
                    <a href="read.php?order=desc&keyword=<?= $keyword ?>">
                        <img src="images/desc.png" alt="降順に並び替え" class="sort-img">
                    </a>
                    <a href="read.php?order=asc&keyword=<?= $keyword ?>">
                        <img src="images/asc.png" alt="昇順に並び替え" class="sort-img">
                    </a>
                    <form action="read.php" method="get" class="search-form">
                        <input type="hidden" name="order" value="<?= $order ?>">
                        <input type="text" class="search-box" placeholder="商品名で検索" name="keyword" value="<?= $keyword ?>">
                    </form>
                </div>
                <a href="create.php" class="btn">商品登録</a>
            </div>
            <table class="products-table">
                <tr>
                    <th>商品コード</th>
                    <th>商品名</th>
                    <th>単価</th>
                    <th>在庫数</th>
                    <th>仕入先コード</th>
                    <th>編集</th>
                    <th>削除</th>
                </tr>
                <?php 
                foreach ($products as $product) {
                    $table_row = "
                    <tr>
                    <td>{$product['product_code']}</td>
                    <td>{$product['product_name']}</td>
                    <td>{$product['price']}</td>
                    <td>{$product['stock_quantity']}</td>
                    <td>{$product['vendor_code']}</td>
                    <td><a href='update.php?id={$product['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
                    <td><a href='delete.php?id={$product['id']}'><img src='images/delete.png' alt='削除' class='delete-icon'></a></td>
                    </tr>
                    ";
                    echo $table_row;
                }
                ?>
            </table>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
    </footer>
</body>
</html>