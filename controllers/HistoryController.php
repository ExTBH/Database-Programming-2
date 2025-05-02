<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Feature.php';
require_once __DIR__ . '/../models/Rental.php';


class HistoryController extends BaseController
{
    public function index()
    {
        $rentals = Rental::getAll();
        $title = "My Rent History";
        ob_start();
        include __DIR__ . '/../views/user/history.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }
}
