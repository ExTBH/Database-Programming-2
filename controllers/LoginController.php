<?php
require_once 'BaseController.php';
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/HomeOwnerRequest.php';

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

        $userTableUser = User::getByEmail($email);

        if ($userTableUser) {
            if ($userTableUser->suspended) {
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
                $_SESSION[USER_SESSION_KEY] = $userTableUser->id;
                echo json_encode([
                    'redirect' => PREFIX . '/index.php'
                ]);
                http_response_code(200); // OK
                return;
            } else {
                // Password doesn't match
                echo json_encode([
                    'error' => 'Invalid email or password.'
                ]);
                http_response_code(401);
                return;
            }
        }

        // User not in `users` table, check `HomeOwnerRequests`
        $userRequestUser = HomeOwnerRequest::getByEmail($email);

        if ($userRequestUser) {
            error_log("User found in HomeOwnerRequests: " . json_encode($userRequestUser));
            if ($userRequestUser->approval_status == 'pending') {
                error_log("User request is pending: " . json_encode($userRequestUser));
                echo json_encode([
                    'error' => 'Your request is awaiting approval.'
                ]);
                http_response_code(403); // Forbidden
                return;
            } elseif ($userRequestUser->approval_status == 'rejected') {
                error_log("User request is rejected: " . json_encode($userRequestUser));
                echo json_encode([
                    'error' => 'Your request has been rejected. You may resubmit your request.'
                ]);
                http_response_code(403); // Forbidden
                return;
            }
        }

        // No user found in either table
        echo json_encode([
            'error' => 'Invalid email or password.'
        ]);
        http_response_code(401);
    }
}
