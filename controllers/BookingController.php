<?php
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/ChargePoint.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class BookingController extends BaseController
{
    public function index()
    {
        $this->render('user/booking', [
            'title' => 'My Bookings'
        ]);
    }

    public function getBookings()
    {
        $user = User::fromSession();
        $bookings = Booking::getByUserId($user->id);

        // Format bookings for JSON response
        $formattedBookings = array_map(function ($booking) {
            return [
                'id' => $booking->booking_id,
                'start_time' => $booking->start_time->format('Y-m-d H:i'),
                'end_time' => $booking->end_time->format('Y-m-d H:i'),
                'status' => $booking->status,
                'price_per_kwh' => ChargePoint::getById($booking->charge_point_id)->price_per_kwh,
                'homeowner_id' => ChargePoint::getById($booking->charge_point_id)->homeowner_id,
            ];
        }, $bookings);

        header('Content-Type: application/json');
        echo json_encode(['bookings' => $formattedBookings]);
        exit;
    }
}
