<?php
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//DB接続関数：db_conn()
function db_conn(){
    try {
        $db_name = "interview_tools_2";    //データベース名
        $db_id   = "root";      //アカウント名
        $db_pw   = "";          //パスワード：XAMPPはパスワード無し or MAMPはパスワード"root"に修正してください。
        $db_host = "localhost"; //DBホスト
        $pdo = new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}



//SQLエラー関数：sql_error($stmt)
function sql_error($stmt){
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}

//リダイレクト関数: redirect($file_name)
function redirect($filename){
    header("Location: ".$filename);
    exit();
}

//SessionCheck(スケルトン)
function sschk():void{
    if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
       exit("Login Error");
    }else{
       session_regenerate_id(true);
       $_SESSION["chk_ssid"] = session_id();
    }
    }

// OpenAI APIとの通信関数
function callOpenAIAPI($prompt) {
    // OpenAI APIのエンドポイントURL
    $url = OPENAI_API_URL;

    // リクエストヘッダーの設定
    $headers = [
        'Authorization: Bearer ' . OPENAI_API_KEY,
        'Content-Type: application/json'
    ];

    // リクエストボディの作成
    $data = [
        'model' => 'gpt-3.5-turbo', // 使用するモデル
        'messages' => [
            ['role' => 'system', 'content' => 'あなたはインタビューの専門家です。与えられた目的に基づいて、適切な質問を1つ生成してください。'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.7 // 0.0から1.0の間で、生成されるテキストのランダム性を調整
    ];

    // cURLでリクエストを送信
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    try {
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        if (isset($responseData['choices'][0]['message']['content'])) {
            return $responseData['choices'][0]['message']['content'];
        } else {
            throw new Exception('APIからの応答が不正です');
        }
    } finally {
        curl_close($ch);
    }
}

// 質問生成関数
function generateQuestion($purpose) {
    try {
        return callOpenAIAPI("目的: $purpose");
    } catch (Exception $e) {
        error_log('Error calling OpenAI API: ' . $e->getMessage());
        return '質問の生成中にエラーが発生しました。';
    }
}
?>