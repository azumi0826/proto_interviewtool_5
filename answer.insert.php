<?PHp

//1. POSTデータ取得 
$question_id = $_POST["question_id"];
$respondent_id = $_POST["respondent_id"];
$answer = $_POST["answer"];


//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "INSERT INTO answers (question_id, respondent_id, answer) VALUES (:question_id, :respondent_id, :answer)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':question_id', $question_id, PDO::PARAM_INT);
$stmt->bindValue(':respondent_id', $respondent_id, PDO::PARAM_INT);
$stmt->bindValue(':answer', $answer, PDO::PARAM_STR);
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
    //*** function化する！*****************
sql_error($stmt);
}else{
    redirect("answer.php");
 
}

// ridirectに入れるページが変わっても使えるように

?>
