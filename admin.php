<?php

require_once 'config.php';
require_once 'controllers/AdminController.php';
require_once 'models/User.php';
require_once 'models/ChargePoint.php';

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
        $user_id = $_POST['user_id'] ?? null;


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

                    case 'suspendUser':
                        $userId = $_POST['user_id'] ?? null;
                        $suspended = $_POST['suspended'] ?? null;
                    
                        if ($userId === null || $suspended === null) {
                            $response = [
                                'success' => false,
                                'message' => 'Missing required fields.'
                            ];
                            break;
                        }
                    
                        try {
                            $success = User::manageUser(
                                'update',
                                $userId,
                                null,
                                null,
                                null,
                                null,
                                null,
                                (bool)$suspended
                            );
                    
                            if ($success) {
                                $response = [
                                    'success' => true,
                                    'message' => 'User suspension status updated.'
                                ];
                            } else {
                                $response = [
                                    'success' => false,
                                    'message' => 'Failed to update suspension status.'
                                ];
                            }
                        } catch (Exception $e) {
                            $response = [
                                'success' => false,
                                'message' => 'Error: ' . $e->getMessage()
                            ];
                        }
                        break;
                        
                        case 'deleteUser':
                            $user_id = $_POST['user_id'] ?? null;
                        
                            if (!$user_id) {
                                $response = [
                                    'success' => false,
                                    'message' => 'User ID is required'
                                ];
                                break;
                            }
                        
                            try {
                                // Call the manageUser method to delete the user
                                User::manageUser('delete', $user_id);
                                $response = [
                                    'success' => true,
                                    'message' => 'User deleted successfully'
                                ];
                            } catch (Exception $e) {
                                $response = [
                                    'success' => false,
                                    'message' => 'Failed to delete user: ' . $e->getMessage()
                                ];
                            }
                            break;

                       
case 'addChargePointForm':
    error_log('addChargePointForm triggered'); // Log when this case is triggered

    $location = $_POST['location'] ?? null;
    $postcode = $_POST['postcode'] ?? null;
    $latitude = isset($_POST['latitude']) ? (float)$_POST['latitude'] : null; // Cast to float
    $longitude = isset($_POST['longitude']) ? (float)$_POST['longitude'] : null; // Cast to float
    $pricePerKwh = isset($_POST['price_per_kwh']) ? (float)$_POST['price_per_kwh'] : null; // Cast to float
    $homeownerEmail = $_POST['homeowner_email'] ?? null;
    $description = $_POST['description'] ?? null;
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;

    // Read the image file content
    $imageContent = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageContent = file_get_contents($_FILES['image']['tmp_name']); // Read the raw image content
    }

    if (!$location || !$postcode || !$latitude || !$longitude || !$pricePerKwh || !$homeownerEmail) {
        error_log('Missing required fields'); // Log missing fields
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
        exit;
    }

    $homeowner = user::getByEmail($homeownerEmail);
    $homeOwnerId =  $homeowner-> id;
    try {
        ChargePoint::manageChargePoint(
            'add',
            null,
            $homeOwnerId, // homeownerId will be resolved in the method
            $location,
            $postcode,
            $latitude,
            $longitude,
            $pricePerKwh, // Pass the float value
            $description,
            $isAvailable,
            $imageContent // Pass the raw image content directly
        );
        echo json_encode(['success' => true, 'message' => 'Charge point added successfully.']);
    } catch (Exception $e) {
        error_log('Error adding charge point: ' . $e->getMessage()); // Log exceptions
        echo json_encode(['success' => false, 'message' => 'Failed to add charge point: ' . $e->getMessage()]);
    }
    exit;

case 'validateHomeownerEmail':
    $email = $_POST['email'] ?? null;

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Email is required.']);
        exit;
    }

    try {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'This user does not exist.']);
        } elseif ($user['role'] !== 'homeowner') {
            echo json_encode(['success' => false, 'message' => 'This user is not a homeowner.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Valid homeowner email.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error validating email: ' . $e->getMessage()]);
    }
    exit;
                               
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