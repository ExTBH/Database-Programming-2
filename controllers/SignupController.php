<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php';

class SignupController extends BaseController
{
    public function index()
    {
        $this->render('auth/signup', [
            'title' => 'Signup'
        ]);
    }

    public function signup($FirstName, $LastName, $email, $password)
    {
        $conn = Database::getInstance()->getConnection();

        // First, check if a user with this email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            http_response_code(409); // Conflict
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Email already exists. Please choose another one.']);
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
            $_SESSION[USER_SESSION_KEY] = User::getByEmail($email)->id;

            // Redirect to the home page or dashboard
            echo json_encode([
                'redirect' => PREFIX . '/index.php'
            ]);
            http_response_code(200); // OK

            exit();
        } else {
            http_response_code(500); // Internal Server Error
            header('Content-Type: application/json');
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['error' => "Error creating account: " . $errorInfo[2]]);
        }
    }

    //create request method

    public function createRequest($first_name, $last_name, $email, $password)
    {
        $conn = Database::getInstance()->getConnection();
        
        $stmt = $conn->prepare("INSERT INTO HomeOwnerRequests(email, first_name, last_name, password, created_at, approval_status, rejection_message) 
                               VALUES (?, ?, ?, ?, NOW(), 'pending', NULL)");
        
        $success = $stmt->execute([
            $email,
            $first_name,
            $last_name,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        if ($success) {
            http_response_code(200);
            echo json_encode([
                'message' => 'Request submitted successfully'
                
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to submit request']);
        }
    }

}
