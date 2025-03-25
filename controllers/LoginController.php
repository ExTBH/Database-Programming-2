<?php
include_once 'BaseController.php';

class LoginController extends BaseController
{
    public function index()
    {
        $title = "Login";
        ob_start();
        include __DIR__ . '/../views/home.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }
}
