<?php

function plugin_telegramintegra_install() {
global $DB;
   $query = "
      CREATE TABLE IF NOT EXISTS `glpi_plugin_telegramintegra` (
         `id` INT AUTO_INCREMENT PRIMARY KEY,
         `name` VARCHAR(255) NOT NULL,
         `bot_token` VARCHAR(255) NOT NULL,
         `state` tinyint NOT NULL DEFAULT 0 COMMENT '0=disable, 1=enable',
         `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB;
   ";
   $DB->query($query);

   return true;
}

function plugin_telegramintegra_uninstall() {
   global $DB;

   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_telegramintegra`");

   return true;
}
