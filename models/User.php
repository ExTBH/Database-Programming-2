<?php
require_once __DIR__ . '/../database.php';

class User
{
    public $id;
    public $name;
    public $email;

    public function __construct($id, $name, $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public static function getAll()
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row['id'], $row['name'], $row['email']);
        }
        return $users;
    }

    public static function getById($id)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new User($row['id'], $row['name'], $row['email']) : null;
    }
}
