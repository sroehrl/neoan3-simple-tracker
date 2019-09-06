<?php


namespace Neoan3\Apps;


use Filebase\Database;
use Filebase\Document;
use Filebase\Filesystem\FilesystemException;
use Filebase\Format\Json;

/**
 * Class SimpleTracker
 *
 * @package Neoan3\Apps
 */
class SimpleTracker
{
    /**
     * @var Database
     */
    static private $_db;

    static private $_requestUrl;

    /**
     * @param bool $config
     *
     * @return Database
     */
    static function init($config = false)
    {
        $path = dirname(dirname(dirname(dirname(__DIR__)))) . '/simple-tracker/';
        $configTemplate = [
            'dir'            => $path . 'data',
            'backupLocation' => $path . 'backup/data',
            'format'         => Json::class,
            'cache'          => true,
            'cache_expires'  => 1800,
            'pretty'         => false,
            'safe_filename'  => true,
            'read_only'      => false
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
        self::$_requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        return self::$_db;

    }

    /**
     * @param bool $identifier
     *
     * @return Database
     */
    static function track($identifier = false)
    {
        if (!self::$_db) {
            self::init();
        }
        if (!$identifier) {
            $identifier = 'rand-' . Ops::hash(4);
        }
        $data = [
            'date'       => date('Y-m-d H:i:s'),
            'endpoint'   => self::$_requestUrl,
            'referrer'   => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            'identifier' => $identifier
        ];
        $document = self::$_db->get('v-'.Ops::hash(8));
        $endpoint = self::$_db->get($data['endpoint']);
        foreach ($data as $key => $value){
            $document->{$key} = $value;
        }
        $endpoint->visits[] = $data;
        $endpoint->save();
        $document->save();

        return self::$_db;
    }

    /**
     * @param $endpoint
     *
     * @return Document
     */
    static function endpointData($endpoint=false)
    {
        if (!self::$_db) {
            self::init();
        }
        if(!$endpoint){
            $endpoint = self::$_requestUrl;
        }
        return self::$_db->get($endpoint);
    }

    /**
     * @param $identifier
     *
     * @return Document
     */
    static function identifierData($identifier){
        if (!self::$_db) {
            self::init();
        }
        return self::$_db->get($identifier);
    }
}
