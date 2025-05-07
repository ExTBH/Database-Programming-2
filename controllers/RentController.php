<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ChargePoint.php';

class RentController extends BaseController
{
    public function index(int $chargePointId): void
    {
        $chargePoint = ChargePoint::getById($chargePointId);

        if (!$chargePoint) {
            // Handle error: Charge point not found
            $this->render('error', ['message' => 'Charge point not found.']);
            return;
        }

        $this->render('user/rent', [
            'title' => 'Rent Charge Point',
            'chargePoint' => $chargePoint,
        ]);
    }

    public function add(int $chargePointId, string $date, string $startTime, string $endTime): void
    {
        header('Content-Type: application/json');

        // Validate input
        if (empty($chargePointId) || empty($date) || empty($startTime) || empty($endTime)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input.']);
            return;
        }

        // Add booking logic here
        // For example, save to database
        echo json_encode([]); // Return empty JSON response
        http_response_code(200);
    }
}
