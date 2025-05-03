<?php
require_once __DIR__ . '/BaseController.php';

class RentController extends BaseController
{
    public function index()
    {
        $this->render('user/rent', [
            'title' => 'My Rentals',
            // 'rentals' => Rental::getAll() // Assuming Rental::getAll() fetches all rentals
        ]);
    }
}
