<?php
$botToken = getenv('8536061723:AAEhGBWglxI2GgYj4uWhnPhRfOZ2_rvyBx8');

if (!$botToken) {
    error_log("ERROR: BOT_TOKEN is not set in Vercel Environment Variables!");
    http_response_code(500);
    die("Token missing");
}

$apiURL = "https://api.telegram.org/bot" . $botToken . "/";
$update = json_decode(file_get_contents('php://input'), true);

error_log("Received update from Telegram: " . json_encode($update));

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'] ?? '';
    $firstName = $update['message']['from']['first_name'] ?? 'User';

    if ($text === '/start') {
        $responseText = "Hello " . $firstName . "! The bot is running successfully on Vercel.";
    } else {
        $responseText = "You said: " . $text;
    }

    $data = [
        'chat_id' => $chatId,
        'text' => $responseText
    ];

    $ch = curl_init($apiURL . 'sendMessage');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($result === false) {
        error_log("Curl Error: " . curl_error($ch));
    } else {
        error_log("Telegram API Response (HTTP " . $httpCode . "): " . $result);
    }
    
    curl_close($ch);
}

http_response_code(200);
echo "OK";
?>
