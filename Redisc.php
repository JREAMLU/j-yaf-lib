<?php

namespace App\Lib;

class Redisc {

    protected static $instance = null;
    private static $_config;

    const REDIS = 'redis';
    const APPLICATION = 'application';
    const CLUSTER = 'cluster';

    public static function instance($db_config) {
        if (!isset(self::$instance)) {
            self::$instance = new Redisc($db_config);
        }
        return self::$instance;
    }

    public function __construct($db_config) {
        self::$_config = $db_config;
    }

    public static function client($name) {
        return self::connect($name);
    }

    public static function clientCluster($name) {
        return self::connectCluster($name);
    }

    private static function connect($name) {
        $redis = new \Redis();
        $redis->connect(
            self::$_config[self::APPLICATION][self::REDIS][$name]['host'],
            self::$_config[self::APPLICATION][self::REDIS][$name]['port'],
            self::$_config[self::APPLICATION][self::REDIS][$name]['timeout'],
            NULL,
            self::$_config[self::APPLICATION][self::REDIS][$name]['reconnect']
        );

        return $redis;
    }

    private static function connectCluster($name) {
        $hostports = self::$_config[self::APPLICATION][self::REDIS][$name][self::CLUSTER]['hostport'];
        $hostports = array_values($hostports->toArray());

        $redisCluster = new \RedisCluster(
            NULL,
            $hostports,
            self::$_config[self::APPLICATION][self::REDIS][$name][self::CLUSTER]['timeout'],
            self::$_config[self::APPLICATION][self::REDIS][$name][self::CLUSTER]['readtimeout']
        );

        return $redisCluster;
    }

}