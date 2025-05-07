<?php
require_once 'BaseController.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php';

class LoginController extends BaseController
{
    public function index()
    {
        $this->render('auth/login', [
            'title' => 'Login'
        ]);
    }

    public function login($email, $password)
    {
        session_start();

        $user = User::getByEmail($email);

        if ($user) {
            if ($user->suspended) {
                echo json_encode([
                    'error' => 'Your account has been suspended. Please contact support.'
                ]);
                http_response_code(403);
                return;
            }

            // Get hashed password
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password'])) {
                $_SESSION[USER_SESSION_KEY] = $user->id;
                echo json_encode([
                    'redirect' => PREFIX . '/index.php'
                ]);
                http_response_code(200); // OK
                exit();
            }
        }

        echo json_encode([
            'error' => 'Invalid email or password.'
        ]);
        http_response_code(401);
    }
}
