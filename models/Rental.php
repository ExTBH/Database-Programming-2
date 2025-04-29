<?php
require_once __DIR__ . '/../database.php';

class Rental
{
    public $id;
    public $address;
    public $price;
    public $start_date;
    public $return_date;
    public $status;

    public function __construct($id, $address, $price, $start_date, $return_date, $status)
    {
        $this->id = $id;
        $this->address = $address;
        $this->price = $price;
        $this->start_date = $start_date;
        $this->return_date = $return_date;
        $this->status = $status;
    }

    public static function getAll()
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // $users[] = new Rental($row['id'], $row['name'], $row['email']);
        }
        return $users;
    }

    public static function getById($id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // return $row ? new Rental($row['id'], $row['name'], $row['email']) : null;
    }
}
