<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Feature.php';
require_once __DIR__ . '/../models/Rental.php';


class HistoryController extends BaseController
{
    public function index()
    {
        $this->render('user/history', [
            'title' => 'My Rent History',
            'rentals' => Rental::getAll()
        ]);
    }
}
