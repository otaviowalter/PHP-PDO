<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require 'vendor/autoload.php';

$con = ConnectionCreator::createConnection();
$studentRespository = new PdoStudentRepository($con);

//Vai desativar a opção do banco de executar as queries recebidas, e vai guardalas pra executar tudo de uma vez só
$con->beginTransaction();

try {
    $studentOne = new Student(
        null,
        'Nico',
        new DateTimeImmutable('1985-05-01')
    );
    $studentRespository->save($studentOne);

    $studentTwo = new Student(
        null,
        'Sérgio Lopes',
        new DateTimeImmutable('1985-05-01')
    );
    $studentRespository->save($studentTwo);

    $con->commit();
} catch (\PDOException $err) {
    echo $err->getMessage();
    $con->rollBack();
}

