<?php
require_once __DIR__ . '/../database.php';

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case HOME_OWNER = 'homeowner';
}

class User
{
    public string $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $role;

    public function __construct(string $id, string $firstName, string $lastName, string $email, UserRole $role)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->role = $role->value;
    }

    public static function fromRow(array $row): User
    {
        return new User(
            $row['user_id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            UserRole::from($row['role'])
        );
    }

    public static function fromSession(): ?User
    {
        if (!isset($_SESSION[USER_SESSION_KEY])) {

            return null;
        }

        $user = $_SESSION[USER_SESSION_KEY];
        return $user;
        // return new User(
        //     $user->id,
        //     $user->firstName,
        //     $user->lastName,
        //     $user->email,
        //     UserRole::from($user->role)
        // );
    }



    public static function getAll()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = self::fromRow($row);
        }
        return $users;
    }

    public static function getById($id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function getByEmail($email)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }
}
