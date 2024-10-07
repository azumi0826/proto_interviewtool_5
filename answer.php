<?php
include("funcs.php");
$pdo = db_conn();

// designテーブルから質問を取得
$sql_questions = "SELECT * FROM design";
$stmt_questions = $pdo->prepare($sql_questions);
$status_questions = $stmt_questions->execute();

if ($status_questions == false) {
    sql_error($stmt_questions);
}

$questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

// respondentテーブルから回答者を取得
$sql_respondents = "SELECT * FROM respondent";
$stmt_respondents = $pdo->prepare($sql_respondents);
$status_respondents = $stmt_respondents->execute();

if ($status_respondents == false) {
    sql_error($stmt_respondents);
}

$respondents = $stmt_respondents->fetchAll(PDO::FETCH_ASSOC);

// 既存の回答を取得
$sql_answers = "SELECT * FROM answers";
$stmt_answers = $pdo->prepare($sql_answers);
$stmt_answers->execute();
$existing_answers = $stmt_answers->fetchAll(PDO::FETCH_ASSOC);

// 既存の回答に簡単にアクセスするための検索用配列を作成
$answer_lookup = [];
foreach ($existing_answers as $answer) {
    $answer_lookup[$answer['question_id']][$answer['respondent_id']] = $answer;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>回答の入力</title>
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
                <h2 class="h4 mb-0">回答の入力</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>把握したいこと</th>
                                <th>質問</th>
                                <?php foreach ($respondents as $respondent): ?>
                                    <th><?= h($respondent['name']) ?> (<?= h($respondent['zokusei']) ?>)</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $question): ?>
                                <tr>
                                    <td><?= h($question['purpose']) ?></td>
                                    <td><?= h($question['question']) ?></td>
                                    <?php foreach ($respondents as $respondent): ?>
                                        <td>
                                            <?php
                                            $answer = $answer_lookup[$question['id']][$respondent['id']] ?? null;
                                            $answer_id = $answer ? $answer['id'] : null;
                                            $answer_text = $answer ? $answer['answer'] : '';
                                            ?>
                                            <form method="POST" action="<?= $answer_id ? 'answer.update.php' : 'answer.insert.php' ?>" class="mb-2">
                                                <div class="form-floating mb-2">
                                                    <textarea class="form-control" id="answer_<?= $question['id'] ?>_<?= $respondent['id'] ?>" name="answer" style="height: 100px" placeholder="回答"><?= h($answer_text) ?></textarea>
                                                    <label for="answer_<?= $question['id'] ?>_<?= $respondent['id'] ?>">回答</label>
                                                </div>
                                                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                                                <input type="hidden" name="respondent_id" value="<?= $respondent['id'] ?>">
                                                <?php if ($answer_id): ?>
                                                    <input type="hidden" name="id" value="<?= $answer_id ?>">
                                                <?php endif; ?>
                                                <button type="submit" class="btn btn-sm btn-primary"><?= $answer_id ? '更新' : '保存' ?></button>
                                                <?php if ($answer_id): ?>
                                                    <a href="answer.delete.php?id=<?= $answer_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</a>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>