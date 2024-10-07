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
$purpose = $_POST["purpose"];
$question = $_POST["question"];


//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$sql="UPDATE design SET purpose=:purpose, question=:question WHERE id=:id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id',      $id,      PDO::PARAM_INT);
$stmt->bindValue(':purpose', $purpose, PDO::PARAM_STR);
$stmt->bindValue(':question',$question,PDO::PARAM_STR);
$status = $stmt->execute(); //実行


//４．データ登録処理後
if($status==false){
    //*** function化する！*****************
sql_error($stmt);
}else{
    redirect("design.php");
 
}

// ridirectに入れるページが変わっても使えるように



?>


