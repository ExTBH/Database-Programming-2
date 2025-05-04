<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ChargePoint.php';

class RentController extends BaseController
{
    public function index()
    {
        $chargePoint = new ChargePoint(
            1,
            201,
            "Building 123, Road 2803, Block 428, Seef",
            "428",
            26.2336,
            50.5860,
            0.25,
            "Fast charger near Seef Mall.",
            true,
            "2024-06-01 10:00:00",
            "2024-06-01 10:00:00"
        );


        $this->render('user/rent', [
            'title' => 'My Rentals',
            'chargePoint' => $chargePoint,
            // 'rentals' => Rental::getAll() // Assuming Rental::getAll() fetches all rentals
        ]);
    }
}
