<?php
//1. idで紐付け
$id = $_GET["id"];

//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "SELECT * FROM design WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

$values = "";
if($status==false) {
  sql_error($stmt);
}

// 1つのレコードを取得
$v =  $stmt->fetch(); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>インタビュー設計 - 更新</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">インタビュー管理システム</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" href="design.php">インタビュー設計</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="respondent.php">インタビュアーリスト</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="answer.php">回答入力</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="analysis_2.php">結果分析</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="user.php">ユーザー登録</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">ログアウト</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>


  <div class="container mt-4">
    <div class="card shadow">
      <div class="card-header bg-secondary text-white">
        <h2 class="h4 mb-0">質問更新</h2>
      </div>
      <div class="card-body">
        <form method="POST" action="design.update.php">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="purpose" name="purpose" value="<?=h($v["purpose"])?>" placeholder="把握したいこと">
            <label for="purpose">把握したいこと</label>
          </div>
          <div class="form-floating mb-3">
            <textarea class="form-control" id="question" name="question" style="height: 100px" placeholder="質問"><?=h($v["question"])?></textarea>
            <label for="question">質問</label>
          </div>
          <input type="hidden" name="id" value="<?=h($v["id"])?>">
          <button type="submit" class="btn btn-primary">更新</button>
          <a href="design.php" class="btn btn-secondary">戻る</a>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>