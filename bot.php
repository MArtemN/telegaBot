<?php
namespace FoodBot;

require_once __DIR__ . "/vendor/autoload.php";

/* установка хука
https://api.telegram.org/bot{botToken}/setWebhook?url=https://7c10-212-12-20-139.ngrok-free.app/bot.php
*/

use FoodBot\Bot\Bot;

$bot = new Bot();
try {
    $bot->startBot();
} catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
    die();
}