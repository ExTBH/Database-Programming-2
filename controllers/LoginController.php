<?php
include_once 'BaseController.php';

class LoginController extends BaseController
{
    public function index()
    {
        $title = "Login";
        ob_start();
        include __DIR__ . '/../views/auth/login.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }

    public function login($email, $password)
    {
        // $user = $this->getUser($email, $password);
        // if ($user) {
        // $_SESSION['user'] = $user;
        // header('Location: /');
        // } else {
        echo "Please implement the login method.";
        // }
    }
}
