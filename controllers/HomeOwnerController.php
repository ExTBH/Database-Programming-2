<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Message.php';

class HomeOwnerController extends BaseController
{
    private string $activeSection;

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_id'])) {
            switch ($_POST['form_id']) {
                case 'markMessageAsRead':
                    $this->handleMarkMessageAsRead();
                    exit;
            }
        }
    }

    private function handleMarkMessageAsRead(): void
    {
        header('Content-Type: application/json');

        if (!isset($_POST['message_id'])) {
            echo json_encode(['success' => false, 'message' => 'Message ID is required']);
            return;
        }

        try {
            Message::markAsRead((int)$_POST['message_id']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to mark message as read']);
        }
    }

    public function index(): void
    {
        $this->activeSection = $_GET['section'] ?? 'charge-points';


        switch ($this->activeSection) {
            case 'charge-points':
                $sectionContent = $this->manageChargePoints();
                break;
            case 'users':
                $sectionContent = $this->manageUserAccounts();
                break;
            case 'messages':
                $sectionContent = $this->manageMessages();
                break;
            default:
                $sectionContent = $this->manageChargePoints();
        }

        $this->render('homeowner/index', [
            'title' => 'Homeowner Dashboard',
            'activeSection' => $this->activeSection,
            'sectionContent' => $sectionContent
        ]);
    }

    private function manageChargePoints(): string
    {

        ob_start();
        require __DIR__ . '/../views/homeowner/sections/charge_points.phtml';
        return ob_get_clean();
    }

    private function manageUserAccounts(): string
    {
        ob_start();
        require __DIR__ . '/../views/homeowner/sections/bookings.phtml';
        return ob_get_clean();
    }

    private function manageMessages(): string
    {
        ob_start();
        require __DIR__ . '/../views/homeowner/sections/messages.phtml';
        return ob_get_clean();
    }
}
