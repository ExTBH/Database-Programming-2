<?php

require_once __DIR__ . '/../database.php';

/**
 * Converts an image file to a Base64-encoded data URL.
 *
 * @param string $imagePath Path to the image file
 * @return string|false The Base64 image URL or false on failure
 */
function imageToBase64Url($imagePath)
{
    if (!file_exists($imagePath)) {
        return false;
    }

    $imageInfo = getimagesize($imagePath);
    if ($imageInfo === false) {
        return false; // Not a valid image
    }

    $mimeType = $imageInfo['mime'];
    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        return false;
    }

    $base64 = base64_encode($imageData);
    return 'data:' . $mimeType . ';base64,' . $base64;
}


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
    public string $image;

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
        string $updated_at,
        string $image
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
        $this->image = $image;
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
            $row['updated_at'],
            $row['image'] ?? null
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

    public static function manageChargePoint(
        string $action,
        ?int $chargePointId = null,
        ?int $homeownerId = null,
        ?string $address = null,
        ?string $postcode = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?float $pricePerKwh = null,
        ?string $description = null,
        ?bool $isAvailable = null
    ): bool {
        $conn = Database::getInstance()->getConnection();

        try {
            switch ($action) {
                case 'add':
                    if (
                        $homeownerId === null || !$address || !$postcode ||
                        $latitude === null || $longitude === null || $pricePerKwh === null || $isAvailable === null
                    ) {
                        throw new InvalidArgumentException("Missing required fields for charge point creation");
                    }

                    $stmt = $conn->prepare("
                        INSERT INTO charge_points 
                            (homeowner_id, address, postcode, latitude, longitude, price_per_kwh, description, is_available, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ");
                    return $stmt->execute([
                        $homeownerId,
                        $address,
                        $postcode,
                        $latitude,
                        $longitude,
                        $pricePerKwh,
                        $description,
                        (int)$isAvailable
                    ]);

                case 'update':
                    if (!$chargePointId) {
                        throw new InvalidArgumentException("Charge point ID is required for update");
                    }

                    $updates = [];
                    $params = [];

                    if ($homeownerId !== null) {
                        $updates[] = "homeowner_id = ?";
                        $params[] = $homeownerId;
                    }
                    if ($address !== null) {
                        $updates[] = "address = ?";
                        $params[] = $address;
                    }
                    if ($postcode !== null) {
                        $updates[] = "postcode = ?";
                        $params[] = $postcode;
                    }
                    if ($latitude !== null) {
                        $updates[] = "latitude = ?";
                        $params[] = $latitude;
                    }
                    if ($longitude !== null) {
                        $updates[] = "longitude = ?";
                        $params[] = $longitude;
                    }
                    if ($pricePerKwh !== null) {
                        $updates[] = "price_per_kwh = ?";
                        $params[] = $pricePerKwh;
                    }
                    if ($description !== null) {
                        $updates[] = "description = ?";
                        $params[] = $description;
                    }
                    if ($isAvailable !== null) {
                        $updates[] = "is_available = ?";
                        $params[] = (int)$isAvailable;
                    }

                    if (empty($updates)) {
                        throw new InvalidArgumentException("No fields provided for update");
                    }

                    $updates[] = "updated_at = NOW()";
                    $params[] = $chargePointId;

                    $query = "UPDATE charge_points SET " . implode(', ', $updates) . " WHERE charge_point_id = ?";
                    $stmt = $conn->prepare($query);
                    return $stmt->execute($params);

                case 'delete':
                    if (!$chargePointId) {
                        throw new InvalidArgumentException("Charge point ID is required for deletion");
                    }

                    $stmt = $conn->prepare("DELETE FROM charge_points WHERE charge_point_id = ?");
                    return $stmt->execute([$chargePointId]);

                default:
                    throw new InvalidArgumentException("Invalid action specified");
            }
        } catch (PDOException $e) {
            error_log("ChargePoint management error: " . $e->getMessage());
            return false;
        }
    }
}
