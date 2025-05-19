<?php

require_once __DIR__ . '/../database.php';



class Booking
{
    /** @var int */
    public $booking_id;

    /** @var int */
    public $charge_point_id;

    /** @var int */
    public $user_id;

    /** @var DateTime */
    public $start_time;

    /** @var DateTime */
    public $end_time;

    /** @var string: pending, approved, declined, cancelled, completed */
    public $status;

    /** @var string */
    public $created_at;

    /** @var string */
    public $updated_at;

    /**
     * @param int $booking_id
     * @param int $charge_point_id
     * @param int $user_id
     * @param DateTime $start_time
     * @param DateTime $end_time
     * @param string $status
     * @param string $created_at
     * @param string $updated_at
     */
    public function __construct(
        $booking_id,
        $charge_point_id,
        $user_id,
        DateTime $start_time,
        DateTime $end_time,
        string $status,
        $created_at,
        $updated_at
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

    /**
     * @param array $row
     * @return Booking
     */
    private static function fromRow($row)
    {
        return new Booking(
            (int)$row['booking_id'],
            (int)$row['charge_point_id'],
            (int)$row['user_id'],
            new DateTime($row['start_time']),
            new DateTime($row['end_time']),
            $row['status'],
            $row['created_at'],
            $row['updated_at']
        );
    }

    /**
     * @return Booking[]
     */
    public static function getAll()
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

    /**
     * @param int $id
     * @return Booking|null
     */
    public static function getById($id)
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

    /**
     * @param int $user_id
     * @return Booking[]
     */
    public static function getByUserId($user_id)
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

    /**
     * @param int $home_owner_id
     * @param int $offset
     * @param int $limit
     * @return Booking[]
     */
    public static function getAllForHomeOwnerId($home_owner_id, $offset = 0, $limit = 10)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare(
            "SELECT b.* FROM bookings b
             INNER JOIN charge_points cp ON b.charge_point_id = cp.charge_point_id
             WHERE cp.homeowner_id = :home_owner_id
             ORDER BY b.booking_id DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':home_owner_id', $home_owner_id, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        $bookings = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = self::fromRow($row);
        }

        return $bookings;
    }

    /**
     * @param int $home_owner_id
     * @return int
     */
    public static function countForHomeOwnerId($home_owner_id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS booking_count FROM bookings b
             INNER JOIN charge_points cp ON b.charge_point_id = cp.charge_point_id
             WHERE cp.homeowner_id = ?"
        );
        $stmt->execute([$home_owner_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['booking_count'] : 0;
    }

    /**
     * @param int $charge_point_id
     * @param int $user_id
     * @param DateTime $start_time
     * @param DateTime $end_time
     * @param string $status
     * @return Booking
     */
    public static function add(
        $charge_point_id,
        $user_id,
        DateTime $start_time,
        DateTime $end_time,
        $status
    ) {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO bookings (charge_point_id, user_id, start_time, end_time, status)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $charge_point_id,
            $user_id,
            $start_time->format('Y-m-d H:i:s'),
            $end_time->format('Y-m-d H:i:s'),
            $status
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

    /**
     * @return int
     */
    public static function countPending()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM bookings WHERE status = ?");
        $stmt->execute(['pending']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['pending_count'] : 0;
    }

    /**
     * @return int
     */
    public static function countCompleted()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS completed_count FROM bookings WHERE status = ?");
        $stmt->execute(['completed']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['completed_count'] : 0;
    }

    /**
     * @param int $bookingId
     * @param string $status
     * @throws Exception
     */
    public static function updateStatus($bookingId, $status)
    {
        try {
            // Get the PDO connection
            $conn = Database::getInstance()->getConnection();

            $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
            $stmt->execute([$status, $bookingId]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Booking not found or status not updated.");
            }
        } catch (PDOException $e) {
            // Handle database errors
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
