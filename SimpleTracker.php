<?php


namespace Neoan3\Apps;


use Filebase\Database;
use Filebase\Filesystem\FilesystemException;
use Filebase\Format\Json;

/**
 * Class SimpleTracker
 * @package Neoan3\Apps
 */
class SimpleTracker
{
    /**
     * @var Database
     */
    static private $_db;

    /**
     * @param bool $config
     *
     * @return Database
     */
    static function init($config = false)
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/simple-tracker/';
        $configTemplate = [
            'dir' => $path . 'data',
            'backupLocation' => $path . 'backup/data',
            'format' => Json::class,
            'cache' => true,
            'cache_expires' => 1800,
            'pretty' => false,
            'safe_filename' => true,
            'read_only' => false
        ];
        if (!$config) {
            $config = $configTemplate;
        } elseif (is_array($config)) {
            foreach ($configTemplate as $key => $value) {
                if (!isset($config[$key])) {
                    $config[$key] = $value;
                }
            }
        } elseif (is_string($config)) {
            $configTemplate['dir'] = $config . '/data';
            $configTemplate['backupLocation'] = $config . '/backup/data';
            $config = $configTemplate;
        }
        try {
            self::$_db = new Database($config);
        } catch (FilesystemException $e) {
            var_dump('Tracking database not established');
            exit();
        }
        return self::$_db;

    }

    /**
     * @param bool $identifier
     *
     * @return \Filebase\Document
     */
    static function track($identifier = false)
    {
        if (!self::$_db) {
            self::init();
        }
        if (!$identifier) {
            $identifier = 'rand-' . Ops::hash(4);
        }
        $db = self::$_db->get($_SERVER['REQUEST_URI']);
        $db->visits[] = [
            'date' => date('Y-m-d H:i:s'),
            'endpoint' => $_SERVER['REQUEST_URI'],
            'referrer' => $_SERVER['HTTP_REFERER'],
            'identifier' => $identifier
        ];

        $db->save();
        return $db;
    }
    static function endpointData($endpoint){
        if (!self::$_db) {
            self::init();
        }
        return self::$_db->get($_SERVER['REQUEST_URI']);

    }
}
