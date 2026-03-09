<?php
namespace GlpiPlugin\TelegramIntegra\Telegram;

class BufferManager{
    private static function defCache(){
        global $GLPI_CACHE;
        return $GLPI_CACHE;
    }

    public static function setUserState($id, $stage){
        $cache = self::defCache();
        $stages = [];
        $stages[$id]['stage'] = $stage;

        return $cache->set($id, $stages);
    }

    public static function getUserState($id){
        $cache = self::defCache();
        $data = $cache->get($id, null);

        return $data[$id]['stage'] ?? null;
    }

    public static function setDataBuffer($id, $key, $content){
        $cache = self::defCache();
        $set_id = $id . '_' . $key;

        return $cache->set($set_id, $content);
    }

    public static function getDataBuffer($id, $key){
        $cache = self::defCache();
        $set_id = $id . '_' . $key;

        return $cache->get($set_id, null);
    }

    public static function clearDataBuffer($id){
        $cache = self::defCache();

        return $cache->delete($id);
    }
}
