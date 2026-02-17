<?php
namespace app\config;

use flight\database\PdoWrapper;
use PDO;

class Db
{
    private static ?PdoWrapper $instance = null;

    public static function getInstance(): PdoWrapper
    {
        if (self::$instance === null) {

            $config = require __DIR__ . '/config.php';
            $db = $config['database'];

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8',
                $db['host'],
                $db['port'],
                $db['dbname']
            );

            self::$instance = new PdoWrapper(
                $dsn,
                $db['user'],
                $db['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$instance;
    }
}
