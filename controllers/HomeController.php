<?php
include_once 'BaseController.php';

class HomeController extends BaseController
{
    public function index()
    {
        $title = "Home";
        ob_start();
        include __DIR__ . '/../views/home.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }
}
