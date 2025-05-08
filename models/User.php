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
    public bool $suspended;
    

    public function __construct(string $id, string $firstName, string $lastName, string $email, UserRole $role, bool $suspended = false)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->role = $role->value;
        $this->suspended = $suspended;
    }

    private static function fromRow(array $row): User
    {
        return new User(
            $row['user_id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            UserRole::from($row['role']),
            (bool)$row['suspended']
        );
    }
    
    public static function fromSession(): ?User
    {
        if (!isset($_SESSION[USER_SESSION_KEY])) {

            return null;
        }

        $userId = $_SESSION[USER_SESSION_KEY];
        return self::getById($userId);
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
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
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
    
    public static function manageUser(
    string $action, 
    ?string $userId = null, 
    ?string $firstName = null, 
    ?string $lastName = null,
    ?string $email = null,
    ?UserRole $role = null,
    ?string $password = null,
    ?bool $suspended = null
): bool {
    $conn = Database::getInstance()->getConnection();
    
    try {
        switch ($action) {
            case 'add':
                if (!$firstName || !$lastName || !$email || !$role || !$password) {
                    throw new InvalidArgumentException("Missing required fields for user creation");
                }
                
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "INSERT INTO users (first_name, last_name, email, role, password, suspended) 
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                return $stmt->execute([$firstName, $lastName, $email, $role->value, $hashedPassword, (int)$suspended]);
                
            case 'update':
                if (!$userId) {
                    throw new InvalidArgumentException("User ID is required for update");
                }
                
                $updates = [];
                $params = [];
                
                if ($firstName) {
                    $updates[] = "first_name = ?";
                    $params[] = $firstName;
                }
                if ($lastName) {
                    $updates[] = "last_name = ?";
                    $params[] = $lastName;
                }
                if ($email) {
                    $updates[] = "email = ?";
                    $params[] = $email;
                }
                if ($role) {
                    $updates[] = "role = ?";
                    $params[] = $role->value;
                }
                if ($password) {
                    $updates[] = "password = ?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
                if ($suspended !== null) {
                    $updates[] = "suspended = ?";
                    $params[] = (int)$suspended;
                }
                
                if (empty($updates)) {
                    throw new InvalidArgumentException("No fields provided for update");
                }
                
                $params[] = $userId;
                $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                return $stmt->execute($params);
                
            case 'delete':
                if (!$userId) {
                    throw new InvalidArgumentException("User ID is required for deletion");
                }
                
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                return $stmt->execute([$userId]);
                
            default:
                throw new InvalidArgumentException("Invalid action specified");
        }
    } catch (PDOException $e) {
        error_log("User management error: " . $e->getMessage());
        return false;
    }
}

public function isSuspended(): bool
{
    return $this->suspended;
}


public static function countAll(): int
{
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? (int)$row['user_count'] : 0;
}
public static function countHomeOwners(): int
{
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users WHERE role = ?");
    $stmt->execute([UserRole::HOME_OWNER->value]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? (int)$row['user_count'] : 0;
}

}
