<?php

class PluginTelegramintegraConfig extends CommonDBTM {   
   static function getTypeName($nb = 0) {
      return __('Configuração do Plugin', 'seuplugin');
   }

   public function registerBot($data = []){
      global $DB;

      $DB->insert('glpi_plugin_telegramintegra', [
         'name'  => $data['name'],
         'bot_token' => $data['token']
      ]);
   }

   public function showBots(){   
      global $DB;

      $query = "SELECT * FROM glpi_plugin_telegramintegra";
      $result = $DB->query($query);

      return $result;
   }

   public function selectedBot($data = []){
      global $DB;
      $DB->update(
         'glpi_plugin_telegramintegra',
         ['state' => 0],
         ['state' => 1]
      );

      $DB->update('glpi_plugin_telegramintegra', [
         'state' => 1
      ], [
         'id' => $data['item_id']
      ]);
   }
}
