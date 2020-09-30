<?php

use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;

require_once 'vendor/autoload.php';

$con = ConnectionCreator::createConnection();

$con->exec("INSERT INTO phones (area_code, number, student_id) VALUES ('48', '666666666', 2)");
exit();
$tables = '
    CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY  KEY, 
        name TEXT, 
        birth_date TEXT
    );
    CREATE TABLE IF NOT EXISTS phones (
        id INTEGER PRIMARY  KEY, 
        area_code TEXT,
        number TEXT, 
        student_id INTEGER,
        FOREIGN KEY (student_id) REFERENCES students(id)
    );

';
$con->exec($tables);