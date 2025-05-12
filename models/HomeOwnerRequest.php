<?php

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/User.php';

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}

class HomeOwnerRequest
{
    public int $id;
    public string $email;
    public string $first_name;
    public string $last_name;
    public string $password;
    public string $created_at;
    public ApprovalStatus $approval_status;
    public ?string $rejection_message;

    public function __construct(
        int $id,
        string $email,
        string $first_name,
        string $last_name,
        string $password,
        string $created_at,
        ApprovalStatus $approval_status,
        ?string $rejection_message
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

    private static function fromRow(array $row): HomeOwnerRequest
    {
        return new HomeOwnerRequest(
            $row['id'],
            $row['email'],
            $row['first_name'],
            $row['last_name'],
            $row['password'],
            $row['created_at'],
            ApprovalStatus::from($row['approval_status']),
            $row['rejection_message']
        );
    }

    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests");
        $stmt->execute();

        $requests = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $requests[] = self::fromRow($row);
        }

        return $requests;
    }

    public static function getById(int $id): ?HomeOwnerRequest
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function getByEmail(string $email): ?HomeOwnerRequest
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM HomeOwnerRequests WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function approveRequest(int $id): bool
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
        $stmt->execute([ApprovalStatus::APPROVED->value, $id]);

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

    public static function rejectRequest(int $id, string $message): bool
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("UPDATE HomeOwnerRequests SET approval_status = ?, rejection_message = ? WHERE id = ?");
        return $stmt->execute([ApprovalStatus::REJECTED->value, $message, $id]);
    }

  

}
