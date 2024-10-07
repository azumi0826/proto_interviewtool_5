<?php
// 必要な関数とデータベース接続を含むファイルを読み込む
include("funcs.php");
$pdo = db_conn();

// 質問を取得
$sql_questions = "SELECT * FROM design";
$stmt_questions = $pdo->prepare($sql_questions);
$stmt_questions->execute();
$questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);

// 回答者を取得
$sql_respondents = "SELECT * FROM respondent";
$stmt_respondents = $pdo->prepare($sql_respondents);
$stmt_respondents->execute();
$respondents = $stmt_respondents->fetchAll(PDO::FETCH_ASSOC);

// 回答を取得
$sql_answers = "SELECT * FROM answers";
$stmt_answers = $pdo->prepare($sql_answers);
$stmt_answers->execute();
$answers = $stmt_answers->fetchAll(PDO::FETCH_ASSOC);

// 回答へ素早くアクセスするための検索用配列を作成
// キー: [質問ID][回答者ID] = 回答内容
$answer_lookup = [];
foreach ($answers as $answer) {
    $answer_lookup[$answer['question_id']][$answer['respondent_id']] = $answer['answer'];
}

// DataTables用にデータを準備
// 各行は [把握したいこと, 質問, 回答者1の回答, 回答者2の回答, ...] の形式
$data = [];
foreach ($questions as $question) {
    $row = [
        $question['purpose'],
        $question['question']
    ];
    foreach ($respondents as $respondent) {
        // 回答がない場合は空文字を設定
        $row[] = $answer_lookup[$question['id']][$respondent['id']] ?? '';
    }
    $data[] = $row;
}

// データをJSON形式にエンコード（JavaScriptで使用するため）
$json_data = json_encode($data);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>インタビュー結果分析</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <style>
        body { padding-top: 60px; }
        #results { width: 100%; }
        #results td { 
            white-space: normal; 
            word-wrap: break-word;
        }
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
                        <a class="nav-link" href="answer.php">回答入力</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="analysis_2.php">結果分析</a>
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
        <h1 class="mb-4">インタビュー結果</h1>
        
        <div class="card shadow">
            <div class="card-body">
                <!-- DataTablesで拡張される表 -->
                <table id="results" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>把握したいこと</th>
                            <th>質問</th>
                            <?php foreach ($respondents as $respondent): ?>
                                <th><?= h($respondent['name']) ?> (<?= h($respondent['zokusei']) ?>)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#results').DataTable({
            data: <?= $json_data ?>,
            columns: [
                { title: '把握したいこと' },
                { title: '質問' },
                <?php foreach ($respondents as $respondent): ?>
                { title: '<?= h($respondent['name']) ?> (<?= h($respondent['zokusei']) ?>)' },
                <?php endforeach; ?>
            ],
            scrollX: true,
            autoWidth: false,
            columnDefs: [
                { 
                    targets: '_all',
                    width: '200px',
                    render: function(data, type, row) {
                        return type === 'display' && data.length > 100 ?
                            data.substr(0, 100) + '...' :
                            data;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Japanese.json'
            }
        });
    });
    </script>
</body>
</html>