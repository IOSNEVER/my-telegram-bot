<?php
// دریافت توکن از متغیرهای محیطی Vercel (امن‌ترین روش)
// اگر هنوز تنظیم نکردید، توکن را مستقیماً اینجا جایگزین کنید
$botToken = getenv('8536061723:AAEhGBWglxI2GgYj4uWhnPhRfOZ2_rvyBx8') ?: 'توکن_ربات_خود_را_اینجا_بگذارید';

$apiURL = "https://api.telegram.org/bot" . $botToken . "/";

// دریافت داده‌های JSON ارسالی از تلگرام
$update = json_decode(file_get_contents('php://input'), true);

// بررسی اینکه آیا پیامی وجود دارد یا خیر
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    $firstName = $update['message']['from']['first_name'] ?? 'کاربر';

    // پاسخ به دستور /start
    if ($text === '/start') {
        $response = [
            'chat_id' => $chatId,
            'text' => "سلام $firstName! 👋\nربات شما با موفقیت روی Vercel راه‌اندازی شد. 🚀"
        ];
        sendMessage($apiURL, $response);
    } 
    // پاسخ به هر پیام دیگر (Echo)
    else {
        $response = [
            'chat_id' => $chatId,
            'text' => "شما گفتید: \n" . $text
        ];
        sendMessage($apiURL, $response);
    }
}

// تابع کمکی برای ارسال پیام به تلگرام
function sendMessage($apiURL, $data) {
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    $context  = stream_context_create($options);
    // اجرای درخواست و جلوگیری از خطا در صورت قطعی موقت تلگرام
    @file_get_contents($apiURL . 'sendMessage', false, $context);
}
?>
