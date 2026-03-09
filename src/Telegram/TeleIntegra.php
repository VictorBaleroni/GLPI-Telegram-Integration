<?php
namespace GlpiPlugin\TelegramIntegra\Telegram;

use DBmysql;
use GlpiPlugin\TelegramIntegra\TelegramToTicket\CallTicket;
use GlpiPlugin\TelegramIntegra\Telegram\BufferManager;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TeleIntegra{
    private $db;
    private $bot;
    private $token;

    public function __construct(){
        global $DB;
        
        $this->db = $DB;
        $this->token = $this->validBot();
        $this->bot = new Client($this->token['bot_token']);
    }

    private function validBot(){
        $iterator = $this->db->request([
            'FROM' => 'glpi_plugin_telegramintegra',
            'WHERE' => ['state' => 1]
        ]);
        return $data = $iterator->current();
    }

    public function botContent($content){
        $json = json_decode(file_get_contents(__DIR__ . '/../JsonStorage/content.json'), true);
        return $json[$content];
    }

    private function handleStartKeyboard($id){
        $keyboard = new InlineKeyboardMarkup([
            [
                ['text' => '📊 Abrir um Chamado', 'callback_data' => 'nameTicket']
            ],
            [
                ['text' => '❓ Ajuda', 'callback_data' => 'help']
            ]
        ]);

        return $this->bot->sendMessage($id, $this->botContent('initBot'), null, false, null, $keyboard);
    }

    private function handleLevelKeyboard($id){
        $keyboard = new InlineKeyboardMarkup([
            [
                ['text' => 'Baixo', 'callback_data' => 'lowLevel'],
                ['text' => 'Medio', 'callback_data' => 'mediumLevel'],
                ['text' => 'Alto', 'callback_data' => 'highLevel']
            ]
        ]);

        return $this->bot->sendMessage($id, $this->botContent('levelTicket'), null, false, null, $keyboard);
    }

    private function handleNameKeyboard($id){
        $keyboard = new InlineKeyboardMarkup([
            [
                ['text' => 'Proximo', 'callback_data' => 'levelTicket'],
                ['text' => 'Editar Texto', 'callback_data' => 'nameTicket']
            ]
        ]);

        return $this->bot->sendMessage($id, $this->botContent('nameTicketButton'), null, false, null, $keyboard);
    }

    private function handleFinishKeyboard($id){
        $keyboard = new InlineKeyboardMarkup([
            [
                ['text' => 'Concluir solicitação', 'callback_data' => 'finishTicket']
            ]
        ]);

        return $this->bot->sendMessage($id, $this->botContent('contentTicketButton'), null, false, null, $keyboard);
    }

    private function handleCancelKeyboard($id){}

    public function sendRecMsg(){
        try {
            $bot = $this->bot;

            $bot->command('start', function ($message) use ($bot) {
                $bot->sendMessage($message->getChat()->getId(), $this->botContent('welcome'));

                $this->handleStartKeyboard($message->getChat()->getId());
            });

            $bot->callbackQuery(function ($callbackQuery) use ($bot) {
                $messageId = $callbackQuery->getMessage()->getMessageId();
                $data = $callbackQuery->getData();
                $chatId = $callbackQuery->getMessage()->getChat()->getId();

                switch($data){
                    case 'nameTicket':
                        BufferManager::setUserState($chatId, 'nameTicket');
                        $bot->sendMessage($chatId, 'Crie um titulo para seu chamado! ✅');    
                    break;
                    case 'levelTicket':
                        $this->handleLevelKeyboard($chatId);
                    break;
                    case 'lowLevel':
                        BufferManager::setDataBuffer($chatId, 'level', 'low');
                        BufferManager::setUserState($chatId, 'contentTicket');
                        $bot->sendMessage($chatId, 'Nivel de urgencia definido como baixo ✅');
                        $bot->sendMessage($chatId, $this->botContent('contentNext'));
                    break;
                    case 'mediumLevel':
                        BufferManager::setDataBuffer($chatId, 'level', 'medium');
                        BufferManager::setUserState($chatId, 'contentTicket');
                        $bot->sendMessage($chatId, 'Nivel de urgencia definido como medio ✅');
                        $bot->sendMessage($chatId, $this->botContent('contentNext'));
                    break;
                    case 'highLevel':
                        BufferManager::setDataBuffer($chatId, 'level', 'high');
                        BufferManager::setUserState($chatId, 'contentTicket');
                        $bot->sendMessage($chatId, 'Nivel de urgencia definido como alto ✅');
                        $bot->sendMessage($chatId, $this->botContent('contentNext'));
                    break;
                    case 'help':
                        BufferManager::setUserState($chatId, 'help');
                        $bot->sendMessage($chatId, '');
                    break;
                    case 'finishTicket':
                        CallTicket::sendToTicket($chatId);
                        BufferManager::clearDataBuffer($chatId);
                        BufferManager::setUserState($chatId, 'finishTicket');
                        $bot->sendMessage($chatId, $this->botContent('finishTicket'));
                    break;
                }
                $bot->answerCallbackQuery($callbackQuery->getId());
            });
            
            $bot->on(function (Update $update) use ($bot) {
                $message = $update->getMessage();
                $chatId = $message->getChat()->getId();

                switch(BufferManager::getUserState($chatId)){
                    case 'nameTicket':
                        BufferManager::setDataBuffer($chatId, 'name', $message->getText());
                        $this->handleNameKeyboard($chatId);
                    break;
                    case 'contentTicket':
                        BufferManager::setDataBuffer($chatId, 'content', $message->getText());
                        $this->handleFinishKeyboard($chatId);
                    break;
                    case 'help':
                    break;
                    case 'finishTicket':
                        $bot->sendMessage($chatId, $this->botContent('connSet'));
                    break;
                }
            }, function () {
                return true;
            });

            $bot->run();
        } catch (\TelegramBot\Api\Exception $e) {
            $e->getMessage();
        }
    }
}
