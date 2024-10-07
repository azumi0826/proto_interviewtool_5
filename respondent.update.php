<?php
//PHP:コード記述/修正の流れ
//1. insert.phpの処理をマルっとコピー。
//   POSTデータ受信 → DB接続 → SQL実行 → 前ページへ戻る
//2. $id = POST["id"]を追加
//3. SQL修正
//   "UPDATE テーブル名 SET 変更したいカラムを並べる WHERE 条件"
//   bindValueにも「id」の項目を追加
//4. header関数"Location"を「select.php」に変更




//1. POSTデータ取得
$id = $_POST["id"];
$name = $_POST["name"];
$zokusei = $_POST["zokusei"];


//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$sql="UPDATE respondent SET name=:name, zokusei=:zokusei WHERE id=:id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id',      $id,      PDO::PARAM_INT);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':zokusei',$zokusei,PDO::PARAM_STR);
$status = $stmt->execute(); //実行


//４．データ登録処理後
if($status==false){
    //*** function化する！*****************
sql_error($stmt);
}else{
    redirect("respondent.php");
}

// ridirectに入れるページが変わっても使えるように



?>


