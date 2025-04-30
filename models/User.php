<?php
require_once __DIR__ . '/../Database.php';

class User
{
    public $id;
    public $FirstName;
    public $LastName;
    public $email;
    public $role;

    public function __construct($FirstName, $LastName, $email, $role)
    {

        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->email = $email;
        $this->role = $role;
    }



    public static function getAll()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['id'],
                $row['FirstName'],
                $row['LastName'],
                $row['email'],
                $row['role']
            );
        }
        return $users;
    }

    public static function getById($id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new User(
            $row['id'],
            $row['FirstName'],
            $row['LastName'],
            $row['email'],
            $row['role']
        ) : null;
    }

    public static function getByEmail($email)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new User(
            $row['id'],
            $row['FirstName'],
            $row['LastName'],
            $row['email'],
            $row['role']
        ) : null;
    }
}
