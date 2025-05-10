<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AdminController extends BaseController
{
    private string $activeSection;

    public function __construct()
    {
        // // Check if user is logged in and is admin
        // if (!isset($_SESSION[USER_SESSION_KEY]) || $_SESSION[USER_SESSION_KEY]['role'] !== 'admin') {
        //     header('Location: ' . PREFIX . '/login.php');
        //     exit;
        // }
    }

    public function index(): void
    {
        $this->activeSection = $_GET['section'] ?? 'reports';

        $sectionContent = match ($this->activeSection) {
            'reports' => $this->systemReports(),
            'charge-points' => $this->manageChargePoints(),
            'users' => $this->manageUserAccounts(),
            'homeowner-requests' => $this->manageHomeownerRequests(),
            default => $this->systemReports()
        };

        $this->render('admin/index', [
            'title' => 'Admin Dashboard',
            'activeSection' => $this->activeSection,
            'sectionContent' => $sectionContent
        ]);
    }

    private function systemReports(): string
    {
        $statistics = [
            'profitToday' => 150.00,
            'userCount' => 45,
            'overallProfit' => 2500.00,
            'completedCount' => 120,
            'overdueCount' => 5
        ];

        ob_start();
        require __DIR__ . '/../views/admin/sections/system_reports.phtml';
        return ob_get_clean();
    }

    private function manageChargePoints(): string
    {
        $chargePoints = []; // TODO: Get from database

        ob_start();
        require __DIR__ . '/../views/admin/sections/charge_points.phtml';
        return ob_get_clean();
    }

    private function manageUserAccounts(): string
    {
        ob_start();
        require __DIR__ . '/../views/admin/sections/user_accounts.phtml';
        return ob_get_clean();
    }

    private function manageHomeownerRequests(): string
    {
        ob_start();
        require __DIR__ . '/../views/admin/sections/homeowner_requests.phtml';
        return ob_get_clean();
    }
}
