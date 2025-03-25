<?php
include_once 'BaseController.php';

class SignupController extends BaseController
{
    public function index()
    {
        $title = "Signup";
        ob_start();
        include __DIR__ . '/../views/auth/signup.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }

    public function signup($first_name, $last_name, $email, $password)
    {
        // $user = $this->getUser($email, $password);
        // if ($user) {
        // $_SESSION['user'] = $user;
        // header('Location: /');
        // } else {
        echo "Please implement the Signup method.";
        // }
    }
}
