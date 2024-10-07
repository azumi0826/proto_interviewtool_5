<?php
//1. POSTデータ取得
$id = $_GET["id"];

//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "DELETE FROM answers WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("answer.php");
}

?>