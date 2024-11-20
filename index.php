<?php

include_once("./app/database/connect.php");

$error_message = array();

if (isset($_POST["submitButton"])){

    if(empty($_POST["username"])){
        $error_message["username"] = "お名前を入力してください。";
    }
    if(empty($_POST["body"])){
        $error_message["body"] = "コメントを入力してください。";
    }

    if(empty($error_message)){
        $post_date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `comment` (`username`, `body`, `post_date`) VALUES (:username, :body, :post_date);";
        $statement = $pdo->prepare($sql);
    
        //値をセット
        $statement->bindParam(":username", $_POST["username"], PDO::PARAM_STR);
        $statement->bindParam(":body", $_POST["body"], PDO::PARAM_STR);
        $statement->bindParam(":post_date", $post_date, PDO::PARAM_STR);
    
        $statement->execute();
    }

}

$comment_array = array();

// コメントデータを取得
$sql = "SELECT * FROM comment";
// prepareメソッド 
$statement = $pdo->prepare($sql);
// 実行した結果をコメントarrayにいれる
$statement->execute();
$comment_array = $statement->fetchAll();
// var_dump($comment_array); // 配列内容を確認（デバッグ用）
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2ch掲示板AI</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <header>
        <h1 class="title">2ちゃんねるAI掲示板</h1>
        <hr>
    </header>
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
                <h1>2チャンネルAI掲示板作ってみた</h1>
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
            <form action="" class="formWrapper" method="POST">
                <div>
                    <input type="submit" value="書き込む" name="submitButton">
                    <label>名前：</label>
                    <input type="text" name="username">
                </div>
                <div>
                    <textarea class="commentTextArea" name="body"></textarea>
                </div>
                <div>
                    <button>AIで平和な２ちゃんねるに変換する</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>