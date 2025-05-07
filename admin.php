<?php

require_once 'config.php';
require_once 'controllers/AdminController.php';
require_once 'models/User.php';

session_start();

$controller = new AdminController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    $formId = $_POST['form_id'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid request'];

    try {
        $first_name = $_POST['first_name'] ?? null;
        $last_name = $_POST['last_name'] ?? null;
        $email = $_POST['email'] ?? null;
        $role = $_POST['role'] ?? null;
        $userRole = $role ? UserRole::from($role) : null;

        switch ($formId) {
            case 'addUserForm':
                $password = $_POST['password'] ?? null;
                if ($first_name && $last_name && $email && $password && $role) {
                    User::manageUser("add", null, $first_name, $last_name, $email, $userRole, $password);
                    $response = ['success' => true, 'message' => 'User added successfully.'];
                } else {
                    $response['message'] = "All fields are required for adding a user.";
                }
                break;

            case 'editUserForm':
                $user_id = $_POST['user_id'] ?? null;
                if ($user_id) {
                    User::manageUser("update", $user_id, $first_name, $last_name, $email, $userRole);
                    $response = ['success' => true, 'message' => 'User updated successfully.'];
                } else {
                    $response['message'] = "User ID is required.";
                }
                break;

                case 'resetPasswordForm':
                   
                    $new_password = $_POST['new_password'] ?? null;
                    $confirm_password = $_POST['confirm_password'] ?? null;
                    
                    // Validate inputs
                    if (empty($confirm_password) || empty($new_password)) {
                        $response = [
                            'success' => false, 
                            'message' => "User ID and new password are required."
                        ];
                        break;
                    }
                
                    if ($new_password !== $confirm_password) {
                        $response = [
                            'success' => false,
                            'message' => "Passwords do not match."
                        ];
                        break;
                    }
                
                    try {
                        User::manageUser("update", $user_id, null, null, null, null, $new_password);
                        $response = [
                            'success' => true, 
                            'message' => 'Password reset successfully'
                        ];
                    } catch (Exception $e) {
                        $response = [
                            'success' => false,
                            'message' => 'Failed to reset password: ' . $e->getMessage()
                        ];
                    }
                    break;

            default:
                $response['message'] = "Unknown form submission.";
        }

        if ($response['success'] && $isAjax) {
            // Capture only the table HTML
            ob_start();
            $users = User::getAll(); // fetch updated data
            include __DIR__ . '/views/admin/sections/user_accounts.phtml'; // include the entire section
            $fullHtml = ob_get_clean();
        
            // Extract only the table-responsive div
            $dom = new DOMDocument();
            libxml_use_internal_errors(true); // suppress HTML5 warnings
            $dom->loadHTML($fullHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            
            // Find the table-responsive div
            $xpath = new DOMXPath($dom);
            $tableDiv = $xpath->query("//div[contains(@class, 'table-responsive')]")->item(0);
            $tableHtml = $tableDiv ? $dom->saveHTML($tableDiv) : '';
            
            $response['html'] = $tableHtml;
        }

    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // fallback: full page reload
    $controller->index();
} else {
    $controller->index();
}
?>