<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('acesso negado!');
}

include("../../../inc/includes.php");

use GlpiPlugin\TelegramIntegra\Telegram\TeleIntegra;

$bot = new TeleIntegra();

$bot->sendRecMsg();
