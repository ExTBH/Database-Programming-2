<?php

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/User.php';



class HomeOwnerRequest
{
    /** @var int */
    public $id;

    /** @var string */
    public $email;

    /** @var string */
    public $first_name;

    /** @var string */
    public $last_name;

    /** @var string */
    public $password;

    /** @var string */
    public $created_at;

    /** @var string: pending, approved, rejected */
    public $approval_status;

    /** @var string|null */
    public $rejection_message;

    /**
     * @param int $id
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $password
     * @param string $created_at
     * @param string $approval_status
     * @param string|null $rejection_message
     */
    public function __construct(
        $id,
        $email,
        $first_name,
        $last_name,
        $password,
        $created_at,
        $approval_status,
        $rejection_message
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->password = $password;
        $this->created_at = $created_at;
        $this->approval_status = $approval_status;
        $this->rejection_message = $rejection_message;
    }

    /**
     * @param array $row
     * @return HomeOwnerRequest
     */
    private static function fromRow($row)
    {
        return new HomeOwnerRequest(
            $row['id'],
            $row['email'],
            $row['first_name'],
            $row['last_name'],
            $row['password'],
            $row['created_at'],
            $row['approval_status'],
            $row['rejection_message']
        );
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return HomeOwnerRequest[]
     */
    public static function getAll($offset = 0, $limit = 10)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();

        $requests = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $requests[] = self::fromRow($row);
        }

        return $requests;
    }

    /**
     * @return int
     */
    public static function countAll()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS request_count FROM HomeOwnerRequests");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['request_count'] : 0;
    }

    /**
     * @param int $id
     * @return HomeOwnerRequest|null
     */
    public static function getById($id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    /**
     * @param string $email
     * @return HomeOwnerRequest|null
     */
    public static function getByEmail($email)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function approveRequest($id)
    {
        $conn = Database::getInstance()->getConnection();

        try {
            $conn->beginTransaction();

            // Get the request by ID
            $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return false;
            }

            // Update request status to approved
            $stmt = $conn->prepare("UPDATE HomeOwnerRequests SET approval_status = ? WHERE id = ?");
            $stmt->execute(['approved', $id]);

            // Create new user with specific column order
            $orderedData = [
                'user_id' => null,
                'email' => $row['email'],
                'password' => $row['password'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'role' => 'homeowner',
                'suspended' => 0
            ];

            error_log("Ordered Data: " . print_r($orderedData, true));

            $columns = implode(', ', array_keys($orderedData));
            $values = implode(', ', array_fill(0, count($orderedData), '?'));

            error_log("INSERT INTO users ($columns) VALUES ($values)");
            error_log("Values: " . print_r(array_values($orderedData), true));

            $stmt = $conn->prepare("INSERT INTO users ($columns) VALUES ($values)");
            $stmt->execute(array_values($orderedData));

            // Delete the request from HomeOwnerRequests
            $stmt = $conn->prepare("DELETE FROM HomeOwnerRequests WHERE id = ?");
            $stmt->execute([$id]);

            $conn->commit();
            return true;
        } catch (PDOException $e) {
            error_log("Error approving request: " . $e->getMessage());
            $conn->rollBack();
            return false;
        }
    }

    /**
     * @param int $id
     * @param string $message
     * @return bool
     */
    public static function rejectRequest($id, $message)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("UPDATE HomeOwnerRequests SET approval_status = ?, rejection_message = ? WHERE id = ?");
        return $stmt->execute(['rejected', $message, $id]);
    }
}
