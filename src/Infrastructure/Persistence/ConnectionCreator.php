<?php


namespace Alura\Pdo\Infrastructure\Persistence;


use PDO;

class ConnectionCreator
{
    public static function createConnection(): PDO
    {
        $dirDb = __DIR__ . '/../../../banco.sqlite';

        $con = new PDO('sqlite:' . $dirDb);
        $con->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
        $con->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        return $con;
    }
}