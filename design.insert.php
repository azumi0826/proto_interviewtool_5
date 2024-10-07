<!-- design.phpでデータを入力したらデータベースへ送信 -->

<?php
//1. POSTデータ取得 
$purpose  = $_POST["purpose"];
$question  = $_POST["question"];


//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare("INSERT INTO design(purpose,question,indate)VALUES(:purpose,:question,sysdate())");
$stmt->bindValue(':purpose',   $purpose,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':question',  $question,  PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
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