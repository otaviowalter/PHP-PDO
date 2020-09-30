<?php

use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;

require_once 'vendor/autoload.php';

$con = ConnectionCreator::createConnection();

$preparedStatement = $con->prepare('DELETE FROM students WHERE id = ?;');
$preparedStatement->bindValue(1,1, PDO::PARAM_INT);
var_dump($preparedStatement->execute());


