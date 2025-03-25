<?php
include_once 'BaseController.php';

class SignupController extends BaseController
{
    public function index()
    {
        $title = "Signup";
        ob_start();
        include __DIR__ . '/../views/home.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }
}
