<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$con = ConnectionCreator::createConnection();
$studentRepository = new PdoStudentRepository($con);

$studentList = $studentRepository->allStudents();

/** @var Student[] $studentList */
//$studentList = $studentRepository->studentsWithPhones();

/*print_r($studentList[1]->phones()[0]->formattedPhone() . PHP_EOL);*/
print_r($studentList);