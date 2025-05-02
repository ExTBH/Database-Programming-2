<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../database.php';

class ProfileController extends BaseController
{
    public function index()
    {
        $title = "Profile Page";
        ob_start();
        include __DIR__ . '/../views/profile.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }

    public function update($first_name, $last_name, $email, $password)
    {
        $conn = Database::getInstance()->getConnection();

        // Hash the password using the separate method
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update the user in the database
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE user_id = ?");
        $success = $stmt->execute([$first_name, $last_name, $email, $hashedPassword, $_SESSION[USER_SESSION_KEY]]);

        if ($success) {
            header('Location: ' . PREFIX . '/profile.php'); // Redirect to homepage
            exit();
        } else {
            echo "Error updating profile.";
        }
    }
}
