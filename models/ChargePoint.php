<?php

require_once __DIR__ . '/../database.php';

class ChargePoint
{
    public int $charge_point_id;
    public int $homeowner_id;
    public string $address;
    public string $postcode;
    public float $latitude;
    public float $longitude;
    public float $price_per_kwh;
    public ?string $description;
    public bool $is_available;
    public string $created_at;
    public string $updated_at;

    public function __construct(
        int $charge_point_id,
        int $homeowner_id,
        string $address,
        string $postcode,
        float $latitude,
        float $longitude,
        float $price_per_kwh,
        ?string $description,
        bool $is_available,
        string $created_at,
        string $updated_at
    ) {
        $this->charge_point_id = $charge_point_id;
        $this->homeowner_id = $homeowner_id;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->price_per_kwh = $price_per_kwh;
        $this->description = $description;
        $this->is_available = $is_available;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    private static function fromRow(array $row): ChargePoint
    {
        return new ChargePoint(
            $row['charge_point_id'],
            $row['homeowner_id'],
            $row['address'],
            $row['postcode'],
            (float)$row['latitude'],
            (float)$row['longitude'],
            (float)$row['price_per_kwh'],
            $row['description'],
            (bool)$row['is_available'],
            $row['created_at'],
            $row['updated_at']
        );
    }
    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM charge_points");
        $stmt->execute();
        $chargePoints = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chargePoints[] = self::fromRow($row);
        }

        return $chargePoints;
    }
    public static function getById(int $id): ?ChargePoint
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM charge_points WHERE charge_point_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return self::fromRow($row);
        }

        return null;
    }
    public static function getByHomeownerId(int $homeowner_id): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM charge_points WHERE homeowner_id = ?");
        $stmt->execute([$homeowner_id]);
        $chargePoints = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $chargePoints[] = self::fromRow($row);
        }

        return $chargePoints;
    }
}
