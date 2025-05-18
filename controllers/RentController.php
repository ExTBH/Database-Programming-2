<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ChargePoint.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/User.php';

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

        // Check if charge point exists
        $chargePoint = ChargePoint::getById($chargePointId);
        if (!$chargePoint) {
            http_response_code(404);
            echo json_encode(['error' => 'Charge point not found.']);
            return;
        }

        // calculate total price
        $startDateTime = new DateTime("$date $startTime");
        $endDateTime = new DateTime("$date $endTime");
        $interval = $startDateTime->diff($endDateTime);
        $hours = $interval->h + ($interval->days * 24);
        $totalPrice = $hours * $chargePoint->price_per_kwh;
        $totalPrice = number_format($totalPrice, 2);

        try {
            Booking::add(
                $chargePointId,
                User::fromSession()->id,
                $startDateTime,
                $endDateTime,
                BookingStatus::Pending,
               // $totalPrice,
            );
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create booking: ' .   $e->getMessage()]);
            return;
        }

        // Add booking logic here
        // For example, save to database
        echo json_encode([]); // Return empty JSON response
        http_response_code(200);
    }
}
