<?php


namespace Alura\Pdo\Infrastructure\Repository;


use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Repository\StudentRepository;
use DateTimeImmutable;
use http\Exception\RuntimeException;
use mysql_xdevapi\Exception;
use PDO;

class PdoStudentRepository implements StudentRepository
{
    private PDO $con;

    public function __construct(PDO $connection)
    {
        $this->con = $connection;
    }

    public function allStudents(): array
    {
        $sql = 'SELECT * FROM students';
        $statement = $this->con->query($sql);

        return $this->hydrateStudentList($statement);
    }

    public function studentsBirthAt(\DateTimeImmutable $birthDate): array
    {
        $sql = 'SELECT * FROM students WHERE birth_date = :birthDate';
        $statement = $this->con->prepare($sql);
        $statement->bindValue(':birthDate', $birthDate->format('Y-m-d'));
        $statement->execute();

        return $this->hydrateStudentList($statement);
    }

    public function hydrateStudentList(\PDOStatement $statement): array
    {
        $studentDataList = $statement->fetchAll();
        $studentList = [];

        foreach ($studentDataList as $studentData) {
            $studentList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new DateTimeImmutable($studentData['birth_date'])
            );
        }

        return $studentList;
    }

    public function save(Student $student): bool
    {
        if ($student->id() === null) {
            return $this->insert($student);
        }

        return $this->update($student);

    }

    public function insert(Student $student): bool
    {
        $sqlInsert = 'INSERT INTO students (name, birth_date) VALUES (:name, :birthDate);';

        $statement = $this->con->prepare($sqlInsert);

        //Setando o atributo de lançar excessoes do PDO isso não é mais necessario
        /*if ($statement === false)
            throw new \Exception($this->con->errorInfo()[2]);*/

        $res = $statement->execute([
            ':name' => $student->name(),
            ':birthDate' => $student->birthDate()->format('Y-m-d')
        ]);

        if ($res) {
            $student->defineId($this->con->lastInsertId());
        }

        return $res;
    }

    public function update(Student $student): bool
    {
        $sqlInsert = 'UPDATE studentes SET name = :name, birth_date = :birthDate WHERE id = :id';

        $statement = $this->con->prepare($sqlInsert);
        $statement->bindValue(':id', $student->id(), PDO::PARAM_INT);

        $res = $statement->execute([
            ':name', $student->name(),
            ':birthDate', $student->birthDate()->format('Y-m-d')
        ]);

        return $res;
    }

    public function remove(Student $student): bool
    {
        $preparedStatement = $this->con->prepare('DELETE FROM students WHERE id = ?;');
        $preparedStatement->bindValue(1,$student->id(), PDO::PARAM_INT);

        return $preparedStatement->execute();
    }

    public function studentsWithPhones(): array
    {
        $sql = '
            SELECT a.id as student_id
                 , a.name
                 , a.birth_date
                 , b.id as phone_id
                 , b.area_code
                 , b.number
            FROM students as a
            JOIN phones as b ON a.id = b.student_id
        ';

        $stmt = $this->con->query($sql);
        $result = $stmt->fetchAll();

        $studentList = [];

        foreach ($result as $row) {
            if (! array_key_exists($row['student_id'], $studentList)) {
                $studentList[$row['student_id']] = new Student(
                    $row['student_id'],
                    $row['name'],
                    new DateTimeImmutable($row['birth_date'])
                );
            }
            $phone = new Phone(
                $row['phone_id'],
                $row['area_code'],
                $row['number']
            );

            $studentList[$row['student_id']]->addPhone($phone);
        }

        return $studentList;
    }
}