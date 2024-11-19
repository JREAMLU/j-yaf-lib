
<?php

namespace App\Lib;

class Redisc
{
    protected static $instance = null;
    private static $_config;

    public const CLUSTER = 'cluster';

    public static function instance($db_config)
    {
        if (!isset(self::$instance)) {
            self::$instance = new Redisc($db_config);
        }
        return self::$instance;
    }

    public function __construct($db_config)
    {
        self::$_config = $db_config;
    }

    public static function client($name)
    {
        return self::connect($name);
    }

    public static function clientCluster($name)
    {
        return self::connectCluster($name);
    }

    private static function connect($name)
    {
        $redis = new \Redis();
        $redis->connect(
            self::$_config[$name]['host'],
            self::$_config[$name]['port'],
            self::$_config[$name]['timeout'],
            null,
            self::$_config[$name]['reconnect']
        );

        return $redis;
    }

    private static function connectCluster($name)
    {
        $hostports = self::$_config[$name][self::CLUSTER]['hostport'];
        $hostports = array_values($hostports->toArray());

        $redisCluster = new \RedisCluster(
            null,
            $hostports,
            self::$_config[$name][self::CLUSTER]['timeout'],
            self::$_config[$name][self::CLUSTER]['readtimeout']
        );

        return $redisCluster;
    }

}
