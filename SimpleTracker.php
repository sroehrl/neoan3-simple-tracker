<?php


namespace Neoan3\Apps;


use Filebase\Database;
use Filebase\Filesystem\FilesystemException;
use Filebase\Format\Json;

class SimpleTracker
{
    static private $_db;
    static function init($config = false){
        $path = dirname(dirname(dirname(__DIR__))).'/simple-tracker/';
        $configTemplate = [
            'dir'            => $path .'data',
            'backupLocation' => $path .'backup/data',
            'format'         => Json::class,
            'cache'          => true,
            'cache_expires'  => 1800,
            'pretty'         => false,
            'safe_filename'  => true,
            'read_only'      => false
        ];
        if(!$config){
           $config = $configTemplate;
        } elseif (is_array($config)){
            foreach ($configTemplate as $key => $value){
                if(!isset($config[$key])){
                    $config[$key] = $value;
                }
            }
        } elseif (is_string($config)){
            $configTemplate['dir'] = $config .'/data';
            $configTemplate['backupLocation'] = $config .'/backup/data';
            $config = $configTemplate;
        }
        try{
            self::$_db = new Database($config);
        } catch (FilesystemException $e){
            var_dump('Tracking database not established');
            exit();
        }
        return true;

    }
}
