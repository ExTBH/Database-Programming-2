<?php

require_once __DIR__ . '/../database.php';

class Booking
{
    public int $booking_id;
    public int $charge_point_id;
    public int $user_id;
    public DateTime $start_time;
    public DateTime $end_time;
    public string $status;
    public ?float $total_price;
    public string $created_at;
    public string $updated_at;

    public function __construct(
        int $booking_id,
        int $charge_point_id,
        int $user_id,
        DateTime $start_time,
        DateTime $end_time,
        string $status,
        ?float $total_price,
        string $created_at,
        string $updated_at
    ) {
        $this->booking_id = $booking_id;
        $this->charge_point_id = $charge_point_id;
        $this->user_id = $user_id;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->status = $status;
        $this->total_price = $total_price;
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
            $row['status'],
            isset($row['total_price']) ? (float)$row['total_price'] : null,
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
}
