<?php

define('PLUGIN_TELEGRAMINTEGRA_VERSION', '1.0.0');

if (file_exists(__DIR__ . '/vendor/autoload.php')) {  
    require_once __DIR__ . '/vendor/autoload.php';
}

function plugin_init_telegramintegra(){
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegramintegra'] = true;

   if (Plugin::isPluginActive('telegramintegra')) {
      $PLUGIN_HOOKS['config_page']['telegramintegra'] = 'front/config.php';
   }
   if (basename($_SERVER['SCRIPT_FILENAME']) === 'config.php'){
      $PLUGIN_HOOKS['add_javascript']['telegramintegra'] = ['js/config.js'];
      $PLUGIN_HOOKS['add_css']['telegramintegra'] = ['css/config.css'];  
   }
}

function plugin_version_telegramintegra() {
   return [
      'name'           => 'Telgram to Glpi',
      'version'        => PLUGIN_TELEGRAMINTEGRA_VERSION,
      'author'         => 'Victor',
      'license'        => 'GPLv2+',
      'homepage'       => 'https://test.com',
      'minGlpiVersion' => '10.0.17'
   ];
}

function plugin_telegramintegra_check_prerequisites() {
   return true;
}

function plugin_telegramintegra_check_config() {
   return true;
}
