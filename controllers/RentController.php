<?php
require_once __DIR__ . '/BaseController.php';

class RentController extends BaseController
{
    public function index()
    {
        $title = "My Rentals";
        ob_start();
        include __DIR__ . '/../views/profile.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }

    public function update($first_name, $last_name, $email, $password)
    {
        // $user = $this->getUser($email, $password);
        // if ($user) {
        // $_SESSION['user'] = $user;
        // header('Location: /');
        // } else {
        echo "Please implement the Update profile method.";
        // }
    }
}
