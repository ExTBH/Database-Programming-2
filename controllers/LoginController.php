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
    session_start();

    $user = User::getByEmail($email);

    if ($user) {
        // You also need to fetch the hashed password to verify
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user'] = $user;
            header('Location: /index.php');
            exit();
        }
    }
    echo "Incorrect email or password.";
}

    
}
