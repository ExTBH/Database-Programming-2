<?php


require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php';


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
    public function signup($FirstName, $LastName, $email, $password)
    {
        $conn = Database::getInstance()->getConnection();

        // First, check if a user with this email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            echo "Email already exists. Please choose another one.";
            return;
        }

        // Hash the password using the separate method
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $success = $stmt->execute([$FirstName, $LastName, $email, $hashedPassword, "user"]);

        if ($success) {
            session_start();

            // Auto-login user
            $_SESSION[USER_SESSION_KEY] = User::getByEmail($email);

            header('Location: ' . PREFIX . ' /index.php'); // Redirect to homepage
            exit();
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error creating account: " . $errorInfo[2];  // Show the detailed database error
        }
    }
}
