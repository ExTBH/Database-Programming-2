<?php
require_once __DIR__ . '/../database.php';



class User
{
    /** @var string */
    public $id;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $email;

    /** @var string */
    public $role;

    /** @var bool */
    public $suspended;

    /**
     * @param string $id
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $role
     * @param bool $suspended
     */
    public function __construct(
        $id,
        $firstName,
        $lastName,
        $email,
        $role,
        $suspended = false
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->role = $role;
        $this->suspended = $suspended;
    }

    /**
     * @param array $row
     * @return User
     */
    private static function fromRow($row)
    {
        return new User(
            $row['user_id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['role'],
            (bool)$row['suspended']
        );
    }

    /**
     * @return User|null
     */
    public static function fromSession()
    {
        if (!isset($_SESSION[USER_SESSION_KEY])) {
            return null;
        }

        $userId = $_SESSION[USER_SESSION_KEY];
        return self::getById($userId);
    }

    /**
     * @return User[]
     */
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

    /**
     * @param string $id
     * @return User|null
     */
    public static function getById($id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public static function getByEmail($email)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    /**
     * @param string $action
     * @param string|null $userId
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $email
     * @param string|null $role
     * @param string|null $password
     * @param bool|null $suspended
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function manageUser(
        $action,
        $userId = null,
        $firstName = null,
        $lastName = null,
        $email = null,
        $role = null,
        $password = null,
        $suspended = null
    ) {
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
                    return $stmt->execute([$firstName, $lastName, $email, $role, $hashedPassword, (int)$suspended]);

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
                        $params[] = $role;
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

    /**
     * @return bool
     */
    public function isSuspended()
    {
        return $this->suspended;
    }

    /**
     * @return int
     */
    public static function countAll()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['user_count'] : 0;
    }

    /**
     * @return int
     */
    public static function countHomeOwners()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users WHERE role = ?");
        $stmt->execute(['homeowner']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['user_count'] : 0;
    }

    /**
     * @return int
     */
    public static function countUsers()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users WHERE role = ?");
        $stmt->execute(['user']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['user_count'] : 0;
    }
}
