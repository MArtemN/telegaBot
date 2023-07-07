<?php
require_once('vendor/autoload.php');
require_once('Db.php');
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class Bot
{
    protected $token = '6169354773:AAGjAa7TeoBhG9e4sOUN-oCLLLIQLNkOXFQ';
    protected $telegram;
    protected $result;
    protected $text;
    protected $chat_id;
    protected $messageId;
    protected $firstName;
    protected $lastName;
    protected $userName;

    /**
     * @throws TelegramSDKException
     */
    public function startBot()
    {
        $this->telegram = new Api($this->token);

        $this->result = $this->telegram->getWebhookUpdate();

        $this->text = $this->result['message']['text'];
        $this->chat_id = $this->result['message']['chat']['id'];
        $this->firstName = $this->result['message']['chat']['first_name'];
        $this->lastName  = $this->result['message']['chat']['last_name'];
        $this->userName = $this->result['message']['chat']['username'];
        $this->messageId = $this->result['message']['message_id'];

        $baseKeyboard = \Telegram\Bot\Keyboard\Keyboard::make([
            'keyboard' => [
                ['Добавить прием пищи', 'Отчеты'],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);

        switch ($this->text) {
            case '/start':
                $reply = $this->helloUser();
                $this->sendBotMessage($reply, $baseKeyboard);
                break;
            case 'Добавить прием пищи':
                $reply = 'Введите название блюда и количество белка через запятую, сначала блюдо, затем количество белка';

                $this->sendBotMessage($reply);
                break;
            case 'Отчеты':
                $reply = 'Выберите за какое время формируем отчет';
                $replyMarkup = \Telegram\Bot\Keyboard\Keyboard::make([
                    'keyboard' => [
                        ['За день', 'За неделю', 'За месяц'],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false
                ]);

                $this->sendBotMessage($reply, $replyMarkup);
                break;
            case 'За день':
                $db = new Db();
                $response = $db->getReport(1);
                $responseString = "Отчет за день:\n\n" .$response;

                $this->sendBotMessage($responseString, $baseKeyboard);
                break;
            case 'За неделю':
                $db = new Db();
                $response = $db->getReport(7);
                $responseString = "Отчет за неделю:\n\n" .$response;

                $this->sendBotMessage($responseString, $baseKeyboard);
                break;
            case 'За месяц':
                $db = new Db();
                $response = $db->getReport(30);
                $responseString = "Отчет за месяц:\n\n" .$response;

                $this->sendBotMessage($responseString, $baseKeyboard);
                break;
            case substr_count($this->text, ",") < 2:
                $foodData = explode(',', $this->text);
                $db = new Db();
                $response = $db->addFood($foodData[0], $foodData[1], $this->userName, $this->messageId);

                $this->sendBotMessage($response, $baseKeyboard);
                break;
            default:
                $reply = 'Непонятный запрос, попробуйте еще раз';
                $this->sendBotMessage($reply, $baseKeyboard);
        }
    }

    protected function helloUser()
    {
        return 'Здравствуйте ' . $this->firstName . ' ' . $this->lastName;
    }

    protected function sendBotMessage($reply, $replyMarkup = false)
    {
        $this->telegram->sendMessage(
            ['chat_id' => $this->chat_id,
            'text' => $reply,
            'parse_mode' => 'HTML',
            'reply_markup' => $replyMarkup]
        );
    }
}
