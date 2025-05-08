<?php

require_once __DIR__ . '/../database.php';

enum BookingStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Declined = 'declined';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}

class Booking
{
    public int $booking_id;
    public int $charge_point_id;
    public int $user_id;
    public DateTime $start_time;
    public DateTime $end_time;
    public BookingStatus $status;
    public string $created_at;
    public string $updated_at;

    public function __construct(
        int $booking_id,
        int $charge_point_id,
        int $user_id,
        DateTime $start_time,
        DateTime $end_time,
        BookingStatus $status,
        string $created_at,
        string $updated_at
    ) {
        $this->booking_id = $booking_id;
        $this->charge_point_id = $charge_point_id;
        $this->user_id = $user_id;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    private static function fromRow(array $row): Booking
    {
        return new Booking(
            (int)$row['booking_id'],
            (int)$row['charge_point_id'],
            (int)$row['user_id'],
            new DateTime($row['start_time']),
            new DateTime($row['end_time']),
            BookingStatus::from($row['status']),
            $row['created_at'],
            $row['updated_at']
        );
    }

    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM bookings");
        $stmt->execute();
        $bookings = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = self::fromRow($row);
        }

        return $bookings;
    }

    public static function getById(int $id): ?Booking
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return self::fromRow($row);
        }

        return null;
    }

    public static function getByUserId(int $user_id): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $bookings = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = self::fromRow($row);
        }

        return $bookings;
    }

    public static function getAllForHomeOwnerId(int $home_owner_id): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare(
            "SELECT b.* FROM bookings b
             INNER JOIN charge_points cp ON b.charge_point_id = cp.charge_point_id
             WHERE cp.homeowner_id = ?"
        );
        $stmt->execute([$home_owner_id]);
        $bookings = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = self::fromRow($row);
        }

        return $bookings;
    }

    public static function add(
        int $charge_point_id,
        int $user_id,
        DateTime $start_time,
        DateTime $end_time,
        BookingStatus $status,
    ): Booking {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO bookings (charge_point_id, user_id, start_time, end_time, status)
             VALUES (?, ?, ?, ?, ?, )"
        );
        $stmt->execute([
            $charge_point_id,
            $user_id,
            $start_time->format('Y-m-d H:i:s'),
            $end_time->format('Y-m-d H:i:s'),
            $status->value,
        ]);

        return new Booking(
            (int)$conn->lastInsertId(),
            $charge_point_id,
            $user_id,
            $start_time,
            $end_time,
            $status,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );
    }

        public static function countPending(): int
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM bookings WHERE status = ?");
        $stmt->execute([BookingStatus::Pending->value]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['pending_count'] : 0;
    }

        public static function countCompleted(): int
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS completed_count FROM bookings WHERE status = ?");
        $stmt->execute([BookingStatus::Completed->value]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['completed_count'] : 0;
    }
}
