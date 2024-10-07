<?php
include("funcs.php");
$pdo = db_conn();

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $sql = "SELECT a.*, d.purpose, d.question, r.name, r.zokusei 
            FROM answers a
            JOIN design d ON a.question_id = d.id
            JOIN respondent r ON a.respondent_id = r.id
            WHERE a.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status == false) {
        sql_error($stmt);
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>回答の詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
    </style>
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
                        <a class="nav-link" href="design.php">インタビュー設計</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="respondent.php">インタビュアーリスト</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="answer.php">回答入力</a>
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
            <div class="card-header bg-success text-white">
                <h2 class="h4 mb-0">回答の詳細</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="answer.update.php">
                    <div class="mb-3">
                        <label class="form-label">把握したいこと：</label>
                        <p class="form-control-static"><?= h($row["purpose"]) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">質問：</label>
                        <p class="form-control-static"><?= h($row["question"]) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">回答者：</label>
                        <p class="form-control-static"><?= h($row["name"]) ?> (<?= h($row["zokusei"]) ?>)</p>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea id="answer" name="answer" class="form-control" style="height: 100px" placeholder="回答"><?= h($row["answer"]) ?></textarea>
                        <label for="answer">回答</label>
                    </div>
                    <input type="hidden" name="id" value="<?= $row["id"] ?>">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="answer.php" class="btn btn-secondary">回答一覧へ戻る</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>