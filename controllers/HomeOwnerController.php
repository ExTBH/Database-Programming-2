<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class HomeOwnerController extends BaseController
{
    private array $allowedSections = ['charge-points', 'users'];
    private string $activeSection;

    public function __construct() {}

    public function index(): void
    {
        $this->activeSection = $_GET['section'] ?? 'charge-points';

        if (!in_array($this->activeSection, $this->allowedSections)) {
            $this->activeSection = 'charge-points';
        }

        $sectionContent = match ($this->activeSection) {
            'charge-points' => $this->manageChargePoints(),
            'users' => $this->manageUserAccounts(),
            default => $this->manageChargePoints()
        };

        $this->render('homeowner/index', [
            'title' => 'Homeowner Dashboard',
            'activeSection' => $this->activeSection,
            'sectionContent' => $sectionContent
        ]);
    }

    private function manageChargePoints(): string
    {
        $chargePoints = []; // TODO: Get from database

        ob_start();
        require __DIR__ . '/../views/homeowner/sections/charge_points.phtml';
        return ob_get_clean();
    }

    private function manageUserAccounts(): string
    {
        $user = []; // TODO: Get from database
        ob_start();
        require __DIR__ . '/../views/homeowner/sections/bookings.phtml';
        return ob_get_clean();
    }
}
