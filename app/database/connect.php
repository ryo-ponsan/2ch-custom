<?php

$user = "ponsan";
$pass = "ponsan";

try{
    $pdo = new PDO('mysql:host=localhost;dbname=2chan-ai', $user, $pass);
    // echo "DBとの接続に成功しました。";
}catch(PDOexception $error){
    echo $error->getMessage();
}
