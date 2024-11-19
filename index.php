<?php

# 値が入っていれば
if (isset($_POST["submitButton"])){
    # スーパーグローバル変数
    $username = $_POST["username"];
    var_dump($username);
    $body = $_POST["body"];
    var_dump($body);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2ch掲示板</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <header>
        <h1 class="title">2ちゃんねるAI掲示板</h1>
        <hr>
    </header>
    <div class="threadWrapper">
        <div class="childWrapper">
            <div class="threadTitle">
                <span>【タイトル】</span>
                <h1>2チャンネルAI掲示板作ってみた</h1>
            </div>
            <section>
                <article>
                    <div class="wrapper">
                        <div class="nameArea">
                            <span>名前：</span>
                            <p class="username">ponsan</p>
                            <time>:2024/11/19 20:00</time>
                        </div>
                        <p class="comment">ハードコメント</p>
                    </div>
                </article>
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
                    <button>AIで２ちゃん風のテキストに変換</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>