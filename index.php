<?php

include_once("./app/database/connect.php");
require_once("./app/openai/client.php");

$error_message = array();

// デバッグログを有効化
ini_set('log_errors', 1);
ini_set('error_log', './debug.log'); // プロジェクトフォルダにログを出力

// フォーム送信デバッグ
var_dump($_POST);

if (isset($_POST["submitButton"])) {
    // var_dump("Submit button clicked");

    if (empty($_POST["username"])) {
        $error_message["username"] = "お名前を入力してください。";
        // var_dump("Error: Username is empty");
    }
    if (empty($_POST["body"])) {
        $error_message["body"] = "コメントを入力してください。";
        // var_dump("Error: Body is empty");
    }

    if (empty($error_message)) {
        $post_date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `comment` (`username`, `body`, `post_date`) VALUES (:username, :body, :post_date);";
        $statement = $pdo->prepare($sql);

        // 値をセット
        $statement->bindParam(":username", $_POST["username"], PDO::PARAM_STR);
        $statement->bindParam(":body", $_POST["body"], PDO::PARAM_STR);
        $statement->bindParam(":post_date", $post_date, PDO::PARAM_STR);

        if ($statement->execute()) {
            var_dump("New comment inserted successfully");
        } else {
            var_dump("Error inserting new comment: " . print_r($statement->errorInfo(), true));
        }
    }
}

$comment_array = array();

// コメントデータを取得
$sql = "SELECT * FROM comment";
$statement = $pdo->prepare($sql);

if ($statement->execute()) {
    $comment_array = $statement->fetchAll();
    // var_dump("Fetched comments: ", $comment_array); // コメントデータを出力
} else {
    // var_dump("Error fetching comments: " . print_r($statement->errorInfo(), true));
}

// AI変換処理
if (isset($_POST["aiButton"])) {
    log_debug("AI button clicked");

    foreach ($comment_array as $comment) {
        $original_comment = $comment["body"];
        log_debug("Processing comment ID {$comment['id']}: {$original_comment}");

        // OpenAI APIでコメントを変換
        $transformed_comment = OpenAIClient::transformComment($original_comment);

        // OpenAI APIのレスポンスをログに記録
        if ($transformed_comment === $original_comment) {
            log_debug("OpenAI did not transform comment ID {$comment['id']}.");
        } else {
            log_debug("Transformed comment for ID {$comment['id']}: {$transformed_comment}");
        }

        // データベースのコメントを更新
        $update_sql = "UPDATE comment SET body = :body WHERE id = :id";
        $update_statement = $pdo->prepare($update_sql);
        $update_statement->bindParam(":body", $transformed_comment, PDO::PARAM_STR);
        $update_statement->bindParam(":id", $comment["id"], PDO::PARAM_INT);

        if ($update_statement->execute()) {
            log_debug("Comment ID {$comment['id']} updated successfully.");
        } else {
            log_debug("Error updating comment ID {$comment['id']}: " . print_r($update_statement->errorInfo(), true));
        }
    }

    // ページをリロードして更新された結果を表示
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

// デバッグログを収集する配列
$debug_logs = [];

function log_debug($message) {
    global $debug_logs;
    $debug_logs[] = $message; // ログメッセージを収集
    error_log($message); // ログファイルにも記録
}



?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2ch掲示板AI</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        /* ローディング用スタイル */
        #loading {
            display: none; /* 初期状態は非表示 */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            text-align: center;
            color: white;
            font-size: 24px;
            line-height: 100vh; /* 縦中央揃え */
        }
    </style>
</head>
<body>
    <header>
        <h1 class="title">2ちゃんねるAI掲示板</h1>
        <hr>
    </header>

    <div id="loading">AIでやさしいせかいにしてます...お待ちください。</div> <!-- ローディング表示 -->

    <?php if(isset($error_message)) : ?>

        <ul class="errorMessage">
            <?php foreach($error_message as $error) : ?>
                <li><?php echo $error;?></li>
            <?php endforeach;?>
        </ul>

    <?php endif; ?>

    <div class="threadWrapper">
        <div class="childWrapper">
            <div class="threadTitle">
                <span>【タイトル】</span>
                <h1>PHPとjQuery使って、2ちゃんねるAI掲示板作ってみた</h1>
            </div>
            <section>
                <?php foreach($comment_array as $comment) : ?>
                    <article>
                        <div class="wrapper">
                            <div class="nameArea">
                                <span>名前：</span>
                                <p class="username"><?php echo $comment["username"]; ?></p>
                                <time>:<?php echo $comment["post_date"]; ?></time>
                            </div>
                            <p class="comment"><?php echo $comment["body"]; ?></p>
                        </div>
                    </article>
                <?php endforeach ?>
            </section>
            <form action="" class="formWrapper" method="POST" id="aiForm">
                <div>
                    <input type="submit" value="書き込む" name="submitButton">
                    <label>名前：</label>
                    <input type="text" name="username">
                </div>
                <div>
                    <textarea class="commentTextArea" name="body"></textarea>
                </div>
                <div>
                    <input type="submit" name="aiButton" value="AIで平和な２ちゃんねるに変換する">
                </div>
            </form>
            <?php if (!empty($debug_logs)) : ?>
    <div class="debug-section" style="background-color: #f9f9f9; border: 1px solid #ddd; margin: 20px; padding: 10px;">
        <h3>Debug Logs</h3>
        <ul>
            <?php foreach ($debug_logs as $log) : ?>
                <li><?php echo htmlspecialchars($log); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("aiForm");
            form.addEventListener("submit", function (event) {
                const aiButton = event.submitter; // どのボタンで送信されたか
                if (aiButton && aiButton.name === "aiButton") {
                    // ローディング表示
                    document.getElementById("loading").style.display = "block";
                }
            });
        });
    </script>

</body>
</html>

