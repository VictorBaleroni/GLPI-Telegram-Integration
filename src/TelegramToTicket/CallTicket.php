<?php
namespace GlpiPlugin\TelegramIntegra\TelegramToTicket;

use Ticket;
use GlpiPlugin\TelegramIntegra\Telegram\BufferManager;

class CallTicket{
    public static function sendToTicket($id){
        $ticket = new Ticket();
        $ticket->add([
            'name' => BufferManager::getDataBuffer($id, 'name'),
            'content' => BufferManager::getDataBuffer($id, 'content')
        ]);
    }
}