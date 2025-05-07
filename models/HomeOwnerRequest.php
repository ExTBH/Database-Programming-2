<?php

require_once __DIR__ . '/../database.php';

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
}
