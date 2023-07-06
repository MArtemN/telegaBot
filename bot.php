<?php
/* установка хука
https://api.telegram.org/bot{botToken}/setWebhook?url=https://7c10-212-12-20-139.ngrok-free.app/bot.php
*/

require_once ('Bot.php');

$bot = new Bot;
try {
    $bot->startBot();
} catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
    die();
}