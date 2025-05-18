<?php

require_once __DIR__ . '/../database.php';

/**
 * Converts image data to a Base64-encoded data URL.
 *
 * @param string $imageData Raw image data
 * @param string $mimeType The MIME type of the image (e.g., 'image/jpeg')
 * @return string|false The Base64 image URL or false on failure
 */
function imageDataToBase64Url($imageData, $mimeType)
{
    if (empty($imageData) || empty($mimeType)) {
        return false;
    }

    $base64 = base64_encode($imageData);
    return 'data:' . $mimeType . ';base64,' . $base64;
}

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

    $imageData = file_get_contents($imagePath);
    if ($imageData === false) {
        return false;
    }

    return imageDataToBase64Url($imageData, $imageInfo['mime']);
}

class ChargePoint
{
    /** @var int */
    public $charge_point_id;

    /** @var int */
    public $homeowner_id;

    /** @var string */
    public $address;

    /** @var string */
    public $postcode;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var float */
    public $price_per_kwh;

    /** @var string|null */
    public $description;

    /** @var bool */
    public $is_available;

    /** @var string */
    public $created_at;

    /** @var string */
    public $updated_at;

    /** @var string */
    public $image;

    /**
     * @param int $charge_point_id
     * @param int $homeowner_id
     * @param string $address
     * @param string $postcode
     * @param float $latitude
     * @param float $longitude
     * @param float $price_per_kwh
     * @param string|null $description
     * @param bool $is_available
     * @param string $created_at
     * @param string $updated_at
     * @param string $image
     */
    public function __construct(
        $charge_point_id,
        $homeowner_id,
        $address,
        $postcode,
        $latitude,
        $longitude,
        $price_per_kwh,
        $description,
        $is_available,
        $created_at,
        $updated_at,
        $image
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

    /**
     * @param array $row
     * @return ChargePoint
     */
    private static function fromRow($row)
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

    /**
     * @return ChargePoint[]
     */
    public static function getAll()
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

    /**
     * @param int $id
     * @return ChargePoint|null
     */
    public static function getById($id)
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

    /**
     * @param int $homeowner_id
     * @return ChargePoint[]
     */
    public static function getByHomeownerId($homeowner_id)
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

    /**
     * @param int $charge_point_id
     * @return string|null
     */
    public static function getEmailById($charge_point_id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("
            SELECT u.email 
            FROM charge_points cp
            JOIN users u ON cp.homeowner_id = u.user_id 
            WHERE cp.charge_point_id = ?
        ");
        $stmt->execute([$charge_point_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['email'] : null;
    }

    /**
     * @param string $action
     * @param int|null $chargePointId
     * @param int|null $homeownerId
     * @param string|null $address
     * @param string|null $postcode
     * @param float|null $latitude
     * @param float|null $longitude
     * @param float|null $pricePerKwh
     * @param string|null $description
     * @param bool|null $isAvailable
     * @param string|null $imageData Raw image data
     * @param string|null $imageMimeType Image MIME type
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function manageChargePoint(
        $action,
        $chargePointId = null,
        $homeownerId = null,
        $address = null,
        $postcode = null,
        $latitude = null,
        $longitude = null,
        $pricePerKwh = null,
        $description = null,
        $isAvailable = null,
        $imageData = null,
        $imageMimeType = null
    ) {
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

                    $base64Image = ($imageData && $imageMimeType) ? imageDataToBase64Url($imageData, $imageMimeType) : null;

                    $stmt = $conn->prepare("
                        INSERT INTO charge_points 
                            (homeowner_id, address, postcode, latitude, longitude, price_per_kwh, description, is_available, created_at, updated_at, image)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)
                    ");

                    return $stmt->execute([
                        $homeownerId,
                        $address,
                        $postcode,
                        $latitude,
                        $longitude,
                        (float)$pricePerKwh,
                        $description,
                        (int)$isAvailable,
                        $base64Image
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
                    if ($imageData !== null && $imageMimeType !== null) {
                        $base64Image = imageDataToBase64Url($imageData, $imageMimeType);
                        $updates[] = "image = ?";
                        $params[] = $base64Image;
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

    /**
     * @return int
     */
    public static function countAll()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) AS charge_point_count FROM charge_points");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['charge_point_count'] : 0;
    }

    /**
     * @param int $chargePointId
     * @return array|null
     */
    public static function getHomeownerByChargePointId($chargePointId)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("
            SELECT u.user_id AS homeowner_id, u.first_name, u.last_name, u.email 
            FROM charge_points cp
            INNER JOIN users u ON cp.homeowner_id = u.user_id
            WHERE cp.charge_point_id = ?
        ");
        $stmt->execute([$chargePointId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param int $chargePointId
     * @return bool
     */
    public static function deleteChargePoint($chargePointId)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("DELETE FROM charge_points WHERE charge_point_id = ?");
        return $stmt->execute([$chargePointId]);
    }

    /**
     * @return bool
     */
    public function update()
    {
        return self::manageChargePoint(
            'update',
            $this->charge_point_id,
            $this->homeowner_id,
            $this->address,
            $this->postcode,
            $this->latitude,
            $this->longitude,
            $this->price_per_kwh,
            $this->description,
            $this->is_available,
            null, // imageData
            null  // imageMimeType - since the instance's image property stores the complete base64 URL
        );
    }
}
